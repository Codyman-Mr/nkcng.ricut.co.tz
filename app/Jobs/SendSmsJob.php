<?php

namespace App\Jobs;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recipients;
    protected $message;
    protected $loanId;

    public function __construct($recipients, ?string $message = null, ?int $loanId = null)
    {
        $this->recipients = is_array($recipients) ? $recipients : [$recipients];
        $this->message = $message;
        $this->loanId = $loanId;
    }

    public function handle()
    {
        try {
            if (empty($this->recipients)) {
                Log::warning('No recipients provided for SMS job', ['loan_id' => $this->loanId]);
                return;
            }

            if (empty($this->message) && !$this->loanId) {
                Log::warning('No message or loan ID provided for SMS job', ['loan_id' => $this->loanId]);
                return;
            }

            // Load loan if provided
            $loan = $this->loanId ? Loan::with(['user', 'payments'])->find($this->loanId) : null;
            if ($this->loanId && !$loan) {
                Log::warning('Loan not found for SMS job', ['loan_id' => $this->loanId]);
                return;
            }

            // Resolve recipients
            $phoneNumbers = [];
            $userMap = [];
            foreach ($this->recipients as $recipient) {
                $normalizedNumber = $this->convertPhoneNumberToInternationalFormat($recipient);
                if ($this->isValidPhoneNumber($normalizedNumber)) {
                    $phoneNumbers[] = $normalizedNumber;
                    $userMap[$normalizedNumber] = null;
                    continue;
                }

                if (is_numeric($recipient)) {
                    $user = User::find($recipient);
                    if ($user && $user->phone_number) {
                        $normalizedNumber = $this->convertPhoneNumberToInternationalFormat($user->phone_number);
                        $phoneNumbers[] = $normalizedNumber;
                        $userMap[$normalizedNumber] = $user;
                    } else {
                        Log::warning('User not found or missing phone number', ['user_id' => $recipient]);
                    }
                } else {
                    Log::warning('Invalid recipient format', ['recipient' => $recipient]);
                }
            }

            if (empty($phoneNumbers)) {
                Log::warning('No valid phone numbers resolved for SMS job', ['loan_id' => $this->loanId]);
                return;
            }

            // Send SMS
            if ($this->message) {
                // Use provided custom message for bulk SMS
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'apiKey' => config('services.africastalking.api_key'),
                ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
                            'username' => config('services.africastalking.username'),
                            'to' => implode(',', $phoneNumbers),
                            'from' => 'NK CNG',
                            'message' => $this->message,
                            'enqueue' => 1,
                        ]);

                if ($response->successful()) {
                    Log::info('Bulk SMS sent successfully', ['recipients' => $phoneNumbers, 'loan_id' => $this->loanId]);
                } else {
                    Log::error('Bulk SMS API error', ['response' => $response->body(), 'loan_id' => $this->loanId]);
                }
            } elseif ($loan) {
                // Individual messages for loan-specific content
                $successCount = 0;
                foreach ($phoneNumbers as $phoneNumber) {
                    $user = $userMap[$phoneNumber] ?? null;
                    $finalMessage = $this->generateLoanMessage($loan, $user, $phoneNumber);
                    if ($this->sendSingleSms($phoneNumber, $finalMessage)) {
                        $successCount++;
                    }
                }
                Log::info('SMS job completed', ['loan_id' => $this->loanId, 'success_count' => $successCount]);
            }
        } catch (\Exception $e) {
            Log::error('SMS job failed: ' . $e->getMessage(), ['loan_id' => $this->loanId, 'trace' => $e->getTraceAsString()]);
        }
    }

    protected function sendSingleSms(string $phoneNumber, string $message): bool
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'apiKey' => config('services.africastalking.api_key'),
            ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
                        'username' => config('services.africastalking.username'),
                        'to' => $phoneNumber,
                        'from' => 'NK CNG',
                        'message' => $message,
                        'enqueue' => 1,
                    ]);

            if ($response->successful()) {
                Log::info('SMS sent successfully', ['to' => $phoneNumber, 'loan_id' => $this->loanId]);
                return true;
            }

            Log::error('SMS API error', ['response' => $response->body(), 'phone_number' => $phoneNumber]);
            return false;
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage(), ['phone_number' => $phoneNumber]);
            return false;
        }
    }

    protected function generateLoanMessage(Loan $loan, ?User $user, string $phoneNumber): string
    {
        $name = $user ? $user->first_name : 'Guarantor';
        $nextDueDate = $this->calculateNextDueDate($loan);

        if ($loan->status === 'pending') {
            // For pending loans, use loan_required_amount
            return "Habari {$name}, Loan #{$loan->id} application submitted! Amount: TZS {$loan->loan_required_amount}. Awaiting approval.";
        }

        // For approved loans with payments
        $amount = $loan->payments->isNotEmpty() ? $loan->payments->last()->amount : $loan->loan_required_amount;
        return "Habari {$name}, Loan #{$loan->id} approved! Payment of TZS {$amount} is being processed. Next due: {$nextDueDate->format('d/m/Y')}.";
    }

    protected function convertPhoneNumberToInternationalFormat(string $phoneNumber): string
    {
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);
        if (preg_match('/^0(6|7|8)\d{8}$/', $phoneNumber)) {
            return '+255' . substr($phoneNumber, 1);
        }
        if (preg_match('/^(?:\+?255)(6|7|8)\d{8}$/', $phoneNumber)) {
            return '+' . ltrim($phoneNumber, '+');
        }
        return $phoneNumber;
    }

    protected function isValidPhoneNumber(string $phoneNumber): bool
    {
        return preg_match('/^\+255(6|7|8)\d{8}$/', $phoneNumber) === 1;
    }

    protected function calculateNextDueDate(Loan $loan): Carbon
    {
        $lastPaymentDate = $loan->payments->max('payment_date')
            ? Carbon::parse($loan->payments->max('payment_date'))
            : Carbon::parse($loan->created_at);

        return match ($loan->payment_plan ?? 'weekly') {
            'weekly' => $lastPaymentDate->copy()->addWeek(),
            'monthly' => $lastPaymentDate->copy()->addMonth(),
            'quarterly' => $lastPaymentDate->copy()->addMonths(3),
            default => $lastPaymentDate->copy()->addWeek(),
        };
    }
}

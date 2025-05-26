<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendSms extends Component
{
    public $recipients = ''; // String input from UI (comma-separated)
    public $message = '';
    public $preview = false;
    public $loanId = null; // Optional loan ID for UI

    protected $rules = [
        'recipients' => 'required|string',
        'message' => 'nullable|string|max:160',
        'loanId' => 'nullable|integer|exists:loans,id',
    ];

    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.send-sms');
    }

    /**
     * Handle UI-triggered SMS sending.
     */
    public function sendMessage()
    {
        $this->validate();

        // Split comma-separated recipients
        $recipientList = array_filter(array_map('trim', explode(',', $this->recipients)));

        if (empty($recipientList)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No valid recipients provided.',
            ]);
            return;
        }

        // Send SMS
        $result = $this->send($recipientList, $this->message ?: null, $this->loanId);

        if (!$result) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to send SMS to some or all recipients.',
            ]);
        }
    }

    /**
     * Send SMS to one or more recipients.
     *
     * @param mixed $recipients Single User ID, phone number, or array of User IDs/phone numbers
     * @param string|null $message Custom message to send (optional, defaults to $this->message)
     * @param int|null $loanId Optional Loan ID for dynamic message generation
     * @return bool
     */
    public function send($recipients, ?string $message = null, ?int $loanId = null): bool
    {
        try {
            // Normalize recipients to array
            $recipients = is_array($recipients) ? $recipients : [$recipients];
            $message = $message ?? $this->message;

            // Validate inputs
            if (empty($recipients)) {
                Log::warning('No recipients provided for SMS');
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'No recipients specified.',
                ]);
                return false;
            }

            if (empty($message) && !$loanId) {
                Log::warning('No message or loan ID provided for SMS');
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'No message provided.',
                ]);
                return false;
            }

            // Resolve recipients (User IDs or phone numbers)
            $phoneNumbers = [];
            foreach ($recipients as $recipient) {
                if (is_numeric($recipient)) {
                    // Assume it's a User ID
                    $user = User::find($recipient);
                    if ($user && $user->phone_number) {
                        $phoneNumbers[] = [
                            'number' => $this->convertPhoneNumberToInternationalFormat($user->phone_number),
                            'user' => $user,
                        ];
                    } else {
                        Log::warning('User not found or missing phone number', ['user_id' => $recipient]);
                    }
                } else {
                    // Assume it's a phone number
                    $phoneNumbers[] = [
                        'number' => $this->convertPhoneNumberToInternationalFormat($recipient),
                        'user' => null,
                    ];
                }
            }

            if (empty($phoneNumbers)) {
                Log::warning('No valid phone numbers resolved for SMS');
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'No valid recipients found.',
                ]);
                return false;
            }

            // Load loan if provided
            $loan = $loanId ? Loan::with(['user', 'payments'])->find($loanId) : null;
            if ($loanId && !$loan) {
                Log::warning('Loan not found', ['loan_id' => $loanId]);
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Specified loan not found.',
                ]);
                return false;
            }

            // Send SMS to each recipient
            $successCount = 0;
            foreach ($phoneNumbers as $recipient) {
                $finalMessage = $message;
                if ($loan) {
                    $finalMessage = $this->generateLoanReminderMessage($loan, $recipient['user']);
                }

                if ($this->sendSingleSms($recipient['number'], $finalMessage)) {
                    $successCount++;
                }
            }

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Sent SMS to $successCount recipient(s).",
            ]);

            return $successCount > 0;
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to send SMS: ' . $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Preview the SMS message for a loan.
     *
     * @param int $loanId
     * @param int|null $userId
     * @return string|null
     */
    public function previewLoanMessage(int $loanId, ?int $userId = null): ?string
    {
        $this->preview = true;

        $loan = Loan::with(['user', 'payments'])->find($loanId);
        if (!$loan) {
            Log::warning('Loan not found for preview', ['loan_id' => $loanId]);
            return null;
        }

        $user = $userId ? User::find($userId) : $loan->user;
        if (!$user) {
            Log::warning('User not found for preview', ['user_id' => $userId]);
            return null;
        }

        return $this->generateLoanReminderMessage($loan, $user);
    }

    /**
     * Send a single SMS to a phone number.
     *
     * @param string $phoneNumber
     * @param string $message
     * @return bool
     */
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
                Log::info('SMS sent successfully', ['to' => $phoneNumber]);
                return true;
            }

            Log::error('SMS API error', ['response' => $response->body()]);
            return false;
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage(), ['phone_number' => $phoneNumber]);
            return false;
        }
    }

    /**
     * Generate a loan reminder message.
     *
     * @param Loan $loan
     * @param User|null $user
     * @return string
     */
    protected function generateLoanReminderMessage(Loan $loan, ?User $user): string
    {
        $user = $user ?? $loan->user;
        $nextDueDate = $this->calculateNextDueDate($loan);

        return "Habari {$user->first_name}, "
            . "Kumbukumbu ya malipo yako ijayo ni tarehe {$nextDueDate->format('d/m/Y')}. "
            . "Tafadhali hakikisha unalipa kwa wakati ili kuepuka malipo ya ziada.";
    }

    /**
     * Convert phone number to international format (+255).
     *
     * @param string $phoneNumber
     * @return string
     */
    protected function convertPhoneNumberToInternationalFormat(string $phoneNumber): string
    {
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);

        if (preg_match('/^0(6|7|8)\d{8}$/', $phoneNumber)) {
            return '+255' . substr($phoneNumber, 1);
        }

        if (preg_match('/^255(6|7|8)\d{8}$/', $phoneNumber)) {
            return '+' . $phoneNumber;
        }

        return $phoneNumber;
    }

    /**
     * Calculate the next due date for a loan.
     *
     * @param Loan $loan
     * @return Carbon
     */
    protected function calculateNextDueDate(Loan $loan): Carbon
    {
        $lastPaymentDate = $loan->payments->max('payment_date')
            ? Carbon::parse($loan->payments->max('payment_date'))
            : Carbon::parse($loan->created_at);

        return match ($loan->payment_plan) {
            'weekly' => $lastPaymentDate->copy()->addWeek(),
            'monthly' => $lastPaymentDate->copy()->addMonth(),
            default => $lastPaymentDate->copy()->addWeek(),
        };
    }
}

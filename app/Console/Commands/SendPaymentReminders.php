<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Jobs\SendSmsJob;

class SendPaymentReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send automatic payment reminders based on payment plans';

    // public function handle()
    // {
    //     $loans = Loan::with(['user', 'payments'])
    //         ->where('status', 'active')
    //         ->get()
    //         ->filter(function ($loan) {
    //             $nextDueDate = $this->calculateNextDueDate($loan);
    //             return Carbon::now()->addDay()->isSameDay($nextDueDate);
    //         });

    //     foreach ($loans as $loan) {
    //         $this->sendReminder($loan);
    //     }

    //     $this->info('Sent ' . $loans->count() . ' payment reminders');
    // }


    public function handle()
    {
        $today = Carbon::today();

        $loans = Loan::with(['user', 'loanPackage', 'payments'])
            ->where('status', 'active')
            ->get();

        foreach ($loans as $loan) {
            $nextDueDate = $this->calculateNextDueDate($loan);
            $daysUntilDue = $today->diffInDays($nextDueDate, false);

            if (in_array($daysUntilDue, [3, 0, -1])) {
                $totalPaid = $loan->payments->sum('paid_amount');
                $remainingAmount = $loan->loanPackage->amount_to_finance - $totalPaid;

                $paymentPlan = $loan->payment_plan;
                $totalWeeks = match ($paymentPlan) {
                    'weekly' => 1,
                    'bi-weekly' => 2,
                    'monthly' => 4,
                    default => 1,
                };

                $installmentAmount = $remainingAmount / $totalWeeks;
                $installmentAmount = round($installmentAmount, 2);

                $message = match ($daysUntilDue) {
                    3 => "Habari {$loan->user->first_name}, kumbuka kulipa TZS {$installmentAmount} kwa mkopo wako #{$loan->id} ifikapo {$nextDueDate->format('d/m/Y')}.",
                    0 => "Habari {$loan->user->first_name}, leo ni siku ya mwisho kulipa TZS {$installmentAmount} kwa mkopo wako #{$loan->id}. Tafadhali lipa leo.",
                    -1 => "Habari {$loan->user->first_name}, umekosa kulipa TZS {$installmentAmount} kwa mkopo wako #{$loan->id} jana. Tafadhali lipa haraka iwezekanavyo.",
                };

                SendSmsJob::dispatch($loan->user->phone_number, $message, $loan->id);
            }
        }
    }

    public function convertPhoneNumberToInternationalFormat(string $phoneNumber)
    {
        if (preg_match('/^06\d{8}|07\d{8}$/', $phoneNumber)) {
            return $phoneNumber = '+255' . substr($phoneNumber, 1);
        } else {
            return $phoneNumber;
        }
    }

    public function calculateNextDueDate($loan)
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

    public function sendReminder($loan)
    {
        $phoneNumber = $this->convertPhoneNumberToInternationalFormat($loan->user->phone_number);

        $nextDueDate = $this->calculateNextDueDate($loan);
        $formattedDate = $nextDueDate->format('d/m/Y');

        $message = "Habari {$loan->user->first_name}, "
            . "Kumbukumbu ya malipo yako ijayo ni tarehe $formattedDate. "
            . "Tafadhali hakikisha unalipa kwa wakati ili kuepuka malipo ya ziada.";

        $enqueue = 1;
        $username = 'MIKE001';
        $apiKey = 'atsk_a37133bcba27a4928705557b9903b016812000533f89a91f06747a289a8654dca1dac55d';

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'apiKey' => $apiKey,
        ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
                    'username' => $username,
                    'to' => $phoneNumber,
                    // 'to' => implode(',', ['+255712924131', '+255746424480']),
                    'from' => 'NK CNG',
                    'message' => $message,
                    'enqueue' => $enqueue,
                ]);

        if ($response->successful()) {
            $this->info("Sent reminder to {$loan->user->phone_number}");
            Log::info("Sent reminder to {$loan->user->phone_number}: {$message}");
        } else {
            $this->error("Failed to send to {$loan->user->phone_number}");
            Log::error("SMS failed: " . $response->body());
        }



    }
}

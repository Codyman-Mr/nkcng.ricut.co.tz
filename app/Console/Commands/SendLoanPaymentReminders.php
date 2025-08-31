<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Loan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendLoanPaymentReminders extends Command
{
    protected $signature = 'loan:send-reminders';
    protected $description = 'Send SMS reminders to borrowers for upcoming loan payments';

    public function handle()
    {
        $apiKey = 'atsk_a37133bcba27a4928705557b9903b016812000533f89a91f06747a289a8654dca1dac55d';
        $username = 'MIKE001';
        $from = 'NK CNG';

        // Pata loans zinazoendelea na payment plan weekly au monthly
        $loans = Loan::where('status', 'approved')
                     ->whereNotNull('loan_end_date')
                     ->get();

        $today = Carbon::today();

        foreach ($loans as $loan) {

            $user = $loan->user;
            if (!$user) {
                Log::warning("Loan ID {$loan->id} haina user.");
                continue;
            }

            $phoneNumber = $this->normalizePhoneNumber($user->phone_number);

            // Tumia Carbon kushughulikia tarehe

            // Jua siku ya mwisho ya kulipa mkopo
            $loanEndDate = Carbon::parse($loan->loan_end_date);

            // Jua siku ya mwisho ya kulipa ni wiki moja (7 days) kabla ya due date
            $weekBeforeDue = $loanEndDate->copy()->subDays(7);

            // Kumbusho kabla ya wiki moja
            if ($today->eq($weekBeforeDue)) {
                $message = "Habari {$user->first_name}, kumbuka kwamba mkopo wako #{$loan->id} utamalizika tarehe {$loanEndDate->format('d-m-Y')}. Tafadhali hakikisha umeanza kulipa kwa wakati.";
                $this->sendSms($username, $apiKey, $from, $phoneNumber, $message);
            }

            // Kumbusho siku ya mwisho ya kulipa (due date)
            if ($today->eq($loanEndDate)) {
                $message = "Habari {$user->first_name}, leo ni siku ya mwisho ya kulipa mkopo wako #{$loan->id}. Tafadhali lipa ili usipate adhabu au penalty.";
                $this->sendSms($username, $apiKey, $from, $phoneNumber, $message);
            }
        }

        $this->info('Loan payment reminders sent successfully.');
    }

    private function sendSms($username, $apiKey, $from, $to, $message)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'apiKey' => $apiKey,
        ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
            'username' => $username,
            'to' => $to,
            'from' => $from,
            'message' => $message,
            'enqueue' => 1,
        ]);

        if ($response->successful()) {
            Log::info("SMS sent to $to: $message");
        } else {
            Log::error("SMS sending failed to $to. Response: " . $response->body());
        }
    }

    private function normalizePhoneNumber($phoneNumber)
    {
        if (preg_match('/^06\d{8}|07\d{8}$/', $phoneNumber)) {
            return '+255' . substr($phoneNumber, 1);
        }
        return $phoneNumber;
    }
}

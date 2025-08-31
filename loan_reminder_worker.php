<?php

date_default_timezone_set('Africa/Dar_es_Salaam');

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

$username = 'MIKE001';
$apiKey = 'atsk_a37133bcba27a4928705557b9903b016812000533f89a91f06747a289a8654dca1dac55d';
$senderName = 'NK CNG';

// Normalize phone numbers to +255 format
function normalizePhoneNumber($phoneNumber)
{
    if (preg_match('/^06\d{8}|07\d{8}$/', $phoneNumber)) {
        return '+255' . substr($phoneNumber, 1);
    }
    if (preg_match('/^\+255\d{9}$/', $phoneNumber)) {
        return $phoneNumber;
    }
    return null;
}

// Function to log SMS to database
function logSms($loanId, $applicant, $phone, $message, $status, $reason = null)
{
    DB::table('sms_logs')->insert([
        'loan_id' => $loanId,
        'applicant_name' => $applicant,
        'phone_number' => $phone,
        'message' => $message,
        'sent_status' => $status,
        'reason' => $reason,
        'sent_at' => Carbon::now(),
    ]);
}

while (true) {
    echo "[" . date('Y-m-d H:i:s') . "] Starting loan reminder check...\n";

    try {
        $today = Carbon::today()->startOfDay();

        $loans = DB::table('loans')->where('status', 'approved')->get();

        foreach ($loans as $loan) {
            if (!$loan->loan_end_date || !$loan->applicant_phone_number) {
                echo "Skipping loan ID {$loan->id} due to missing date or phone.\n";
                continue;
            }

            $dueDate = Carbon::parse($loan->loan_end_date)->startOfDay();
            $daysLeft = (int) $today->diffInDays($dueDate, false);
            $planRaw = $loan->loan_payment_plan;
            $plan = strtolower(trim(str_replace("'", '', $planRaw)));

            echo "Loan ID: {$loan->id} | Applicant: {$loan->applicant_name} | Loan End Date: {$dueDate->toDateString()} | Days Left: {$daysLeft} | Plan: '{$plan}'\n";

            if (in_array($plan, ['weekly', 'bi-weekly', 'monthly']) && in_array($daysLeft, [1, 0, -1])) {
                $phoneNumber = normalizePhoneNumber($loan->applicant_phone_number);

                if (!$phoneNumber) {
                    echo "Invalid phone number for Loan ID: {$loan->id}\n";
                    logSms($loan->id, $loan->applicant_name, $loan->applicant_phone_number, '', 'not sent', 'Invalid phone number');
                    continue;
                }

                if ($daysLeft === 1) {
                    $message = "Reminder: Your loan from NK CNG is due tomorrow. Please pay on time to avoid penalties.";
                } elseif ($daysLeft === 0) {
                    $message = "Reminder: Today is the final day to repay your loan from NK CNG. Please make the payment today to avoid penalties.";
                } elseif ($daysLeft === -1) {
                    $message = "Notice: You have exceeded your NK CNG loan repayment date by 1 day. Please settle the payment immediately to avoid further penalties.";
                }

                echo "Sending SMS to {$phoneNumber}...\n";

                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'apiKey' => $apiKey,
                ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
                    'username' => $username,
                    'to' => $phoneNumber,
                    'from' => $senderName,
                    'message' => $message,
                    'enqueue' => 1,
                ]);

                if ($response->successful()) {
                    echo "✅ SMS sent to {$phoneNumber} successfully.\n";
                    Log::info("SMS reminder sent to {$phoneNumber} for loan ID {$loan->id}");
                    logSms($loan->id, $loan->applicant_name, $phoneNumber, $message, 'sent');
                } else {
                    $errorMsg = $response->body();
                    echo "❌ Failed to send SMS to {$phoneNumber}. Response: {$errorMsg}\n";
                    Log::error("Failed to send SMS to {$phoneNumber} for loan ID {$loan->id}: {$errorMsg}");
                    logSms($loan->id, $loan->applicant_name, $phoneNumber, $message, 'not sent', $errorMsg);
                }

            } else {
                echo "Condition NOT met for Loan ID {$loan->id}: plan = '{$plan}', daysLeft = {$daysLeft}\n";
                logSms($loan->id, $loan->applicant_name, $loan->applicant_phone_number, '', 'not sent', "Condition not met: plan = '{$plan}', daysLeft = {$daysLeft}");
            }
        }

        echo "Loan reminder check complete.\n";
    } catch (\Exception $e) {
        echo "❗ Error: " . $e->getMessage() . "\n";
        Log::error('Loan reminder worker error: ' . $e->getMessage());
    }

    echo "⏳ Sleeping for 60 seconds before next check...\n\n";
    sleep(60);
}

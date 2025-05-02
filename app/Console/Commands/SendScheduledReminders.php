<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScheduledReminder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SendScheduledReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send scheduled loan reminder messages';

    public function handle()
    {
        $now = Carbon::now();
        $dueReminders = ScheduledReminder::where('status', 'pending')
                            ->where('scheduled_at', '<=', $now)
                            ->get();

        $sent = 0;
        $failed = 0;

        foreach ($dueReminders as $reminder) {
            try {
                $message = $reminder->message ?: $this->defaultMessage($reminder);
                $phone = $this->convertPhoneNumber($reminder->user->phone_number);

                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'apiKey' => config('services.africastalking.api_key'),
                ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
                    'username' => config('services.africastalking.username'),
                    'to' => $phone,
                    'from' => 'NK CNG',
                    'message' => $message,
                    'enqueue' => 1,
                ]);

                if ($response->successful()) {
                    $reminder->update(['status' => 'sent']);
                    $sent++;
                } else {
                    throw new \Exception($response->body());
                }

            } catch (\Exception $e) {
                $reminder->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
                Log::error("Reminder failed: " . $e->getMessage());
                $failed++;
            }
        }

        $this->info("✅ Sent: $sent | ❌ Failed: $failed");
    }

    private function defaultMessage($reminder)
    {
        $date = Carbon::parse($reminder->due_date)->format('d/m/Y');
        return "Habari {$reminder->user->first_name}, Kumbukumbu ya malipo yako ijayo ni tarehe $date. Tafadhali hakikisha unalipa kwa wakati.";
    }

    private function convertPhoneNumber($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (preg_match('/^0(6|7|8)\d{8}$/', $phone)) {
            return '+255' . substr($phone, 1);
        }
        if (preg_match('/^255(6|7|8)\d{8}$/', $phone)) {
            return '+' . $phone;
        }
        return $phone;
    }
}




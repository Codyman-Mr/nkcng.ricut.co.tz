<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;


class TestMsg extends Component
{

    public $testPhoneNumber;
    public $showPreview = false;

    public function render()
    {
        return view('livewire.test-msg');
    }

    public function sendTestMessage()
    {
        // Authorization check
        // $this->authorize('sendMessages');

        // get authenticated user
        $user = Auth::user();

        // Rate limiting (3 requests per minute)
        if (RateLimiter::tooManyAttempts('test-sms', 3)) {
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'You have reached the maximum number of test messages allowed. Please try again in a minute.'
            ]);
            return;
        }

        // Validate phone number
        if (empty($user->phone_number)) {
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'Please update your phone number in your profile settings before sending a test message.'
            ]);
            return;
        }


        try {
            // Create mock loan using user's real data
            $loan = new Loan([
                'payment_plan' => 'weekly',
                'created_at' => now(),
            ]);

            // Simulate relationships
            $loan->setRelation('user', $user);
            $loan->setRelation('payments', collect());


            // Send actual reminder
            $this->sendReminder($loan);

            // Record successful attempt
            RateLimiter::hit('test-sms:' . $user->id, 60);

            // Send success notification
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Test message sent to: {$user->phone_number}"
            ]);
        } catch (\Exception $e) {
            Log::error('Test Message Failed' . $e->getMessage());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'An error occurred while sending the test message. Please try again later.' . $e->getMessage()
            ]);
        }

    }


    public function previewMessage()
    {
        $user = Auth::user();

        $loan = new Loan([
            'payment_plan' => 'weekly',
            'created_at' => now(),
        ]);

        $loan->setRelation('user', $user);

        $nextDueDate = $this->calculateNextDueDate($loan);

        return "Habari {$user->first_name}, "
            . "Kumbukumbu ya malipo yako ijayo ni tarehe {$nextDueDate->format('d/m/Y')}. "
            . "Tafadhali hakikisha unalipa kwa wakati ili kuepuka malipo ya ziada.";
    }


    protected function sendReminder($loan)
    {
        try {
            $phoneNumber = $this->convertPhoneNumberToInternationalFormat($loan->user->phone_number);
            $nextDueDate = $this->calculateNextDueDate($loan);
            $formattedDate = $nextDueDate->format('d/m/Y');

            $message = "Habari {$loan->user->first_name}, "
                . "Kumbukumbu ya malipo yako ijayo ni tarehe $formattedDate. "
                . "Tafadhali hakikisha unalipa kwa wakati ili kuepuka malipo ya ziada.";

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
                Log::info("Reminder sent to {$phoneNumber}");
                return true;
            }

            Log::error("API Error: " . $response->body());
            throw new \Exception("SMS API request failed: " . $response->body());

        } catch (\Exception $e) {
            Log::error("sendReminder failed: " . $e->getMessage());
            throw $e;
        }
    }

    protected function convertPhoneNumberToInternationalFormat(string $phoneNumber)
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


    protected function calculateNextDueDate($loan)
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

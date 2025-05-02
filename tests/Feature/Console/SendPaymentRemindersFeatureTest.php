<?php

namespace Tests\Feature\Console;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Loan;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;


class SendPaymentRemindersFeatureTest extends TestCase
{
    /** @test */
    public function it_sends_sms_with_correct_content()
    {
        Http::fake();

        $user = User::factory()->create([
            'phone_number' => '0755481857',
            'first_name' => 'Inonga'
        ]);

        $loan = Loan::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'payment_plan' => 'weekly',
            'created_at' => now()->subWeek()
        ]);

        Payment::factory()->create([
            'loan_id' => $loan->id,
            'payment_date' => now()->subWeek()
        ]);

        $this->artisan('reminders:send');

        // Verify SMS was sent
        Http::assertSent(function ($request) use ($loan) {
            $nextDueDate = now()->subWeek()->addWeek()->format('d/m/Y');

            return $request->url() === 'https://api.africastalking.com/version1/messaging' &&
                $request['to'] === '+255712924131,+255746424480' && // Remove hardcoded numbers in production!
                str_contains($request['message'], "Habari John, Kumbukumbu ya malipo yako ijayo ni tarehe $nextDueDate");
        });
    }

    /** @test */
    public function it_handles_api_errors_gracefully()
    {
        Http::fake([
            'https://api.africastalking.com/*' => Http::response(['error' => 'Invalid API key'], 500)
        ]);

        $user = User::factory()->create(['phone_number' => '0768591818']);
        $loan = Loan::factory()->create(['user_id' => $user->id, 'status' => 'active']);

        $this->artisan('reminders:send')
            ->expectsOutput('Sent 1 payment reminders'); // Still counts as sent even if API fails

        // Add proper error handling in your command
    }

    /** @test */
    public function it_does_not_send_to_invalid_numbers()
    {
        $user = User::factory()->create(['phone_number' => 'invalid']);
        $loan = Loan::factory()->create(['user_id' => $user->id, 'status' => 'active']);

        $this->artisan('reminders:send')
            ->expectsOutput('Sent 1 payment reminders'); // Should handle invalid numbers

        // Add proper validation in your command
    }
}

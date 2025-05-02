<?php
namespace Tests\Unit\Console;

use Tests\TestCase;
use App\Models\Loan;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Console\Commands\SendPaymentReminders;

class SendReminderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Mock the HTTP client
        Http::fake();

        // Mock the current time
        Carbon::setTestNow(now());

        // Disable logging for cleaner test output
        Log::spy();
    }

    /** @test */
    public function it_selects_loans_with_due_dates_tomorrow()
    {
        // Create test user
        $user = User::factory()->create([
            'phone_number' => '0737740649',
            'first_name' => 'John'
        ]);

        // Create loan with due date tomorrow (weekly plan)
        $loan = Loan::factory()->create([
            'user_id' => $user->id,
            'status' => 'approved',
            'loan_payment_plan' => 'weekly',
            'created_at' => now()->subWeek()
        ]);

        // Create payment with last payment date
        Payment::factory()->create([
            'loan_id' => $loan->id,
            'payment_date' => now()->subWeek()
        ]);

        // Create another loan that's not due
        $nonDueLoan = Loan::factory()->create([
            'status' => 'approved',
            'loan_payment_plan' => 'monthly',
            'created_at' => now()->subDay()
        ]);

        $this->artisan('reminders:send')
            ->assertExitCode(0);
    }

    /** @test */
    // public function it_calculates_next_due_dates_correctly()
    // {
    //     $command = new SendPaymentReminders;

    //     // Weekly plan
    //     $loan = Loan::factory()->make(['loan_payment_plan' => 'weekly']);
    //     $loan->created_at = now()->subWeeks(2);
    //     $lastPayment = now()->subWeek();
    //     $loan->setRelation('payments', collect([
    //         new Payment(['payment_date' => $lastPayment])
    //     ]));

    //     $this->assertEquals(
    //         $lastPayment->addWeek()->format('Y-m-d'),
    //         $command->calculateNextDueDate($loan)->format('Y-m-d')
    //     );

    //     // Monthly plan
    //     $loan->payment_plan = 'monthly';
    //     $this->assertEquals(
    //         $lastPayment->addMonth()->format('Y-m-d'),
    //         $command->calculateNextDueDate($loan)->format('Y-m-d')
    //     );

    //     // No payments yet
    //     $newLoan = Loan::factory()->make(['loan_payment_plan' => 'weekly']);
    //     $newLoan->created_at = now()->subDay();
    //     $newLoan->setRelation('payments', collect());

    //     $this->assertEquals(
    //         $newLoan->created_at->addWeek()->format('Y-m-d'),
    //         $command->calculateNextDueDate($newLoan)->format('Y-m-d')
    //     );
    // }

    // tests/Unit/Console/SendPaymentRemindersTest.php

    /** @test */
    public function it_calculates_next_due_dates_correctly()
    {
        $command = new SendPaymentReminders;

        // Weekly plan test
        $loan = Loan::factory()->make(['payment_plan' => 'weekly']);
        $lastPaymentDate = Carbon::create(2025, 3, 21);

        $loan->created_at = Carbon::create(2025, 3, 1);
        $loan->setRelation('payments', collect([
            new Payment(['payment_date' => $lastPaymentDate])
        ]));

        $this->assertEquals(
            '2025-03-28',  // Weekly plan should add 1 week
            $command->calculateNextDueDate($loan)->format('Y-m-d')
        );

        // Monthly plan test
        $loan->payment_plan = 'monthly';
        $this->assertEquals(
            '2025-04-21',  // Monthly plan should add 1 month
            $command->calculateNextDueDate($loan)->format('Y-m-d')
        );

        // No payments scenario
        $newLoan = Loan::factory()->make(['payment_plan' => 'weekly']);
        $newLoan->created_at = Carbon::create(2025, 3, 21);
        $newLoan->setRelation('payments', collect());

        $this->assertEquals(
            '2025-03-28',  // Should use created_at date + 1 week
            $command->calculateNextDueDate($newLoan)->format('Y-m-d')
        );
    }



    /** @test */
    public function it_converts_phone_numbers_correctly()
    {
        $command = new SendPaymentReminders;

        $this->assertEquals('+255768591818', $command->convertPhoneNumberToInternationalFormat('0768591818'));
        $this->assertEquals('+255756795969', $command->convertPhoneNumberToInternationalFormat('0756795969'));
        $this->assertEquals('+255712345678', $command->convertPhoneNumberToInternationalFormat('0712345678'));
        $this->assertEquals('123456', $command->convertPhoneNumberToInternationalFormat('123456'));
    }
}

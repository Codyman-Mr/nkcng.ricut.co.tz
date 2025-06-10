<?php

namespace Tests\Feature\Livewire;

use App\Livewire\LoanApproval;
use App\Jobs\InitiatePaymentJob;
use App\Jobs\SendSmsJob;
use App\Events\PaymentStatusUpdated;
use App\Models\User;
use App\Models\Loan;
use App\Models\Installation;
use App\Models\CustomerVehicle;
use App\Models\CylinderType;
use App\Models\GovernmentGuarantor;
use App\Models\PrivateGuarantor;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class LoanApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $loan;
    protected $installation;
    protected $cylinderType;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->user = User::factory()->create([
            'id' => 103,
            'first_name' => 'Vince',
            'last_name' => 'Richard',
            'phone_number' => '+255747822160',
        ]);

        $this->cylinderType = CylinderType::factory()->create([
            'id' => 1,
            'name' => '7L Cylinder',
            'capacity' => 7,
            'loan_package_id' => 1,
        ]);

        $vehicle = CustomerVehicle::factory()->create([
            'user_id' => $this->user->id,
            'model' => 'Toyota',
            'plate_number' => 'ABC123',
            'vehicle_type' => 'car',
            'fuel_type' => 'petrol',
        ]);

        $this->installation = Installation::factory()->create([
            'customer_vehicle_id' => $vehicle->id,
            'cylinder_type_id' => $this->cylinderType->id,
            'status' => 'pending',
            'payment_type' => 'loan',
        ]);

        $this->loan = Loan::factory()->create([
            'id' => 1,
            'user_id' => $this->user->id,
            'installation_id' => $this->installation->id,
            'loan_required_amount' => 1400000,
            'status' => 'pending',
        ]);

        GovernmentGuarantor::factory()->create([
            'loan_id' => $this->loan->id,
            'first_name' => 'Qwerty',
            'last_name' => 'Dfghj',
            'phone_number' => '+255746424480',
            'nida_no' => '123456123456',
        ]);

        PrivateGuarantor::factory()->create([
            'loan_id' => $this->loan->id,
            'first_name' => 'Vince',
            'last_name' => 'Dtcy',
            'phone_number' => '+255799999999',
            'nida_no' => '9999999999999999',
        ]);

        // Act as the user
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_mounts_with_correct_initial_data()
    {
        Livewire::test(LoanApproval::class, ['loan' => $this->loan])
            ->assertSet('loanId', $this->loan->id)
            ->assertSet('cylinderType', $this->installation->cylinder_type_id)
            ->assertSet('loanRequiredAmount', 1400000)
            ->assertSet('loanPaymentPlan', 'weekly')
            ->assertSet('phoneNumber', '+255747822160')
            ->assertSet('paymentAmount', 140000)
            ->assertSet('provider', 'Mpesa')
            ->assertSet('paymentStatus', 'Not started');
    }

    /** @test */
    public function it_approves_loan_and_redirects_to_home()
    {
        Queue::fake();
        Event::fake();
        Http::fake([
            'https://api.payment-provider.com/initiate' => Http::response(['status' => 'success'], 200),
        ]);

        Livewire::test(LoanApproval::class, ['loan' => $this->loan])
            ->set('cylinderType', 1)
            ->set('loanRequiredAmount', 1400000)
            ->set('loanPaymentPlan', 'weekly')
            ->set('loanEndDate', '2026-05-30')
            ->set('paymentAmount', 140000)
            ->set('phoneNumber', '+255747822160')
            ->set('provider', 'Mpesa')
            ->call('approveLoan')
            ->assertRedirect('/');

        // Assert database updates
        $this->assertDatabaseHas('loans', [
            'id' => $this->loan->id,
            'status' => 'approved',
            'loan_required_amount' => 1400000,
            'loan_payment_plan' => 'weekly',
            'loan_end_date' => '2026-05-30',
        ]);

        $this->assertDatabaseHas('installations', [
            'id' => $this->installation->id,
            'cylinder_type_id' => 1,
        ]);

        // Assert jobs dispatched
        Queue::assertPushed(InitiatePaymentJob::class, function ($job) {
            return $job->loanId === $this->loan->id &&
                $job->amount === 140000 &&
                $job->phoneNumber === '+255747822160' &&
                $job->provider === 'Mpesa';
        });

        Queue::assertPushed(SendSmsJob::class, function ($job) {
            return $job->recipients === ['+255747822160', '+255746424480', '+255799999999'] &&
                $job->message === "Loan #{$this->loan->id} approved! Payment of TZS 140000 is being processed." &&
                $job->loanId === $this->loan->id;
        });

        // Assert flash message
        $this->assertEquals('Loan approval initiated. You will be notified once the payment is processed.', session('message'));
    }

    /** @test */
    public function it_broadcasts_payment_status_on_successful_payment()
    {
        Queue::fake();
        Event::fake();
        Http::fake([
            'https://api.payment-provider.com/initiate' => Http::response(['status' => 'success'], 200),
        ]);

        // Simulate job execution
        $job = new InitiatePaymentJob($this->loan->id, 140000, '+255747822160', 'Mpesa');
        $job->handle();

        // Assert payment record
        $this->assertDatabaseHas('payments', [
            'loan_id' => $this->loan->id,
            'amount' => 140000,
            'phone_number' => '+255747822160',
            'provider' => 'Mpesa',
            'job_status' => 'completed',
        ]);

        // Assert event broadcast
        Event::assertDispatched(PaymentStatusUpdated::class, function ($event) {
            return $event->userId === $this->user->id &&
                $event->loanId === $this->loan->id &&
                $event->status === 'completed' &&
                str_contains($event->message, 'Payment of TZS 140000 for Loan #1 initiated successfully');
        });
    }

    /** @test */
    public function it_broadcasts_payment_status_on_failed_payment()
    {
        Queue::fake();
        Event::fake();
        Http::fake([
            'https://api.payment-provider.com/initiate' => Http::response(['error' => 'Payment failed'], 400),
        ]);

        // Simulate job execution
        $job = new InitiatePaymentJob($this->loan->id, 140000, '+255747822160', 'Mpesa');
        $job->handle();

        // Assert payment record
        $this->assertDatabaseHas('payments', [
            'loan_id' => $this->loan->id,
            'amount' => 140000,
            'phone_number' => '+255747822160',
            'provider' => 'Mpesa',
            'job_status' => 'failed',
        ]);

        // Assert event broadcast
        Event::assertDispatched(PaymentStatusUpdated::class, function ($event) {
            return $event->userId === $this->user->id &&
                $event->loanId === $this->loan->id &&
                $event->status === 'failed' &&
                str_contains($event->message, 'Payment of TZS 140000 for Loan #1 failed');
        });
    }

    /** @test */
    public function it_rejects_loan_with_valid_reason()
    {
        Livewire::test(LoanApproval::class, ['loan' => $this->loan])
            ->call('openRejectionModal')
            ->assertSet('showRejectModal', true)
            ->set('rejection_reason', 'Insufficient credit score')
            ->call('rejectLoan')
            ->assertRedirect(route('users'));

        $this->assertDatabaseHas('loans', [
            'id' => $this->loan->id,
            'status' => 'rejected',
            'rejection_reason' => 'Insufficient credit score',
        ]);

        $this->assertEquals('Loan rejected successfully.', session('message'));
    }

    /** @test */
    public function it_fails_validation_with_invalid_data()
    {
        Livewire::test(LoanApproval::class, ['loan' => $this->loan])
            ->set('cylinderType', null)
            ->set('loanRequiredAmount', 500)
            ->set('loanPaymentPlan', 'invalid')
            ->set('loanEndDate', '2025-05-30')
            ->set('paymentAmount', 500)
            ->set('phoneNumber', 'invalid')
            ->set('provider', 'InvalidProvider')
            ->call('approveLoan')
            ->assertHasErrors([
                'cylinderType',
                'loanRequiredAmount',
                'loanPaymentPlan',
                'loanEndDate',
                'paymentAmount',
                'phoneNumber',
                'provider',
            ]);
    }

    /** @test */
    public function it_rolls_back_on_database_error()
    {
        Queue::fake();
        Event::fake();

        // Mock database to throw an exception
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->never();
        DB::shouldReceive('rollback')->once();
        $this->loan->shouldReceive('update')->andThrow(new \Exception('Database error'));

        Livewire::test(LoanApproval::class, ['loan' => $this->loan])
            ->set('cylinderType', 1)
            ->set('loanRequiredAmount', 1400000)
            ->set('loanPaymentPlan', 'weekly')
            ->set('loanEndDate', '2026-05-30')
            ->set('paymentAmount', 140000)
            ->set('phoneNumber', '+255747822160')
            ->set('provider', 'Mpesa')
            ->call('approveLoan')
            ->assertNotRedirected();

        $this->assertDatabaseMissing('loans', [
            'id' => $this->loan->id,
            'status' => 'approved',
        ]);

        $this->assertStringContainsString('Failed to approve loan: Database error', session('error'));
    }

    /** @test */
    public function it_checks_payment_status_correctly()
    {
        Payment::factory()->create([
            'loan_id' => $this->loan->id,
            'amount' => 140000,
            'phone_number' => '+255747822160',
            'provider' => 'Mpesa',
            'job_status' => 'completed',
        ]);

        Livewire::test(LoanApproval::class, ['loan' => $this->loan])
            ->call('checkPaymentStatus')
            ->assertSet('paymentStatus', 'completed')
            ->assertSessionHas('message', 'Payment initiated successfully! Please complete the transaction on your phone.');

        // Test failed payment
        Payment::factory()->create([
            'loan_id' => $this->loan->id,
            'amount' => 140000,
            'phone_number' => '+255747822160',
            'provider' => 'Mpesa',
            'job_status' => 'failed',
        ]);

        Livewire::test(LoanApproval::class, ['loan' => $this->loan])
            ->call('checkPaymentStatus')
            ->assertSet('paymentStatus', 'failed')
            ->assertSessionHas('error', 'Payment failed. Please try again or contact support.');
    }
}

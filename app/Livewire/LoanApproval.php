<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Loan;
use App\Models\CylinderType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LoanApproval extends Component
{
    public Loan $loan;
    public $cylinder_type, $loan_required_amount, $loan_payment_plan;
    public $showRejectModal = false;
    public $rejection_reason = '';
    public $loan_end_date = '';

    public function mount(Loan $loan)
    {
        $this->loan = $loan->load(['user', 'documents', 'installation.customerVehicle']);
        $this->loan_required_amount = number_format($loan->loan_required_amount);
        $this->loan_payment_plan = $loan->loan_payment_plan;
        $this->cylinder_type = optional($loan->installation)->cylinder_type_id;
    }

    public function rules()
    {
        return [
            'cylinder_type' => 'required|exists:cylinder_types,id',
            'loan_required_amount' => 'required|string',
            'loan_payment_plan' => 'required|string',
            'loan_end_date' => [
                'required',
                'date',
                'after:' . now()->addYear()->format('Y-m-d'),
            ],
            'rejection_reason' => 'required_if:showRejectModal,true|string|min:5',
        ];
    }

    public function approveLoan()
    {
        Log::info('Loan Approval started', ['loan_id' => $this->loan->id]);
        Log::info('Validating values', [
            'cylinder_type' => $this->cylinder_type,
            'loan_required_amount' => $this->loan_required_amount,
            'loan_payment_plan' => $this->loan_payment_plan,
            'loan_end_date' => $this->loan_end_date,
        ]);

        $this->validateOnly([
            'cylinder_type',
            'loan_required_amount',
            'loan_payment_plan',
            'loan_end_date',
        ]);
        Log::info('Validation passed');

        DB::beginTransaction();
        Log::info('Database transaction started');

        try {
            Log::info('Updating loan values', [
                'loan_required_amount' => str_replace(',', '', $this->loan_required_amount),
                'loan_payment_plan' => $this->loan_payment_plan,
                'loan_end_date' => $this->loan_end_date,
                'status' => 'approved',
            ]);
            $loanUpdated = $this->loan->update([
                'loan_required_amount' => str_replace(',', '', $this->loan_required_amount),
                'loan_payment_plan' => $this->loan_payment_plan,
                'loan_end_date' => $this->loan_end_date,
                'status' => 'approved',
            ]);
            Log::info('Loan update result', ['updated' => $loanUpdated]);

            if ($this->loan->installation) {
                Log::info('Updating installation', ['cylinder_type_id' => $this->cylinder_type]);
                $installationUpdated = $this->loan->installation->update([
                    'cylinder_type_id' => $this->cylinder_type,
                ]);
                Log::info('Installation update result', ['updated' => $installationUpdated]);
            } else {
                Log::warning('No installation found for loan', ['loan_id' => $this->loan->id]);
            }

            DB::commit();
            Log::info('Database transaction committed');

            // Send SMS notification
            $this->sendApprovalNotification($this->loan->user, $this->loan);

            session()->flash('message', 'Loan approved successfully.');
            Log::info('Redirecting to home');
            return $this->redirect('/');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error occurred: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }

        if (config('app.debug')) {
            Log::debug('Query log', DB::getQueryLog());
        }
    }

    public function openRejectionModal()
    {
        $this->showRejectModal = true;
    }

    public function rejectLoan()
    {
        $this->validateOnly(['rejection_reason']);

        $updated = $this->loan->update([
            'status' => 'rejected',
            'rejection_reason' => $this->rejection_reason,
        ]);
        Log::info('Loan rejection update', ['updated' => $updated]);

        $this->showRejectModal = false;
        session()->flash('message', 'Loan rejected.');
        return $this->redirect('/');
    }

    /**
     * Send SMS notification to the user after loan approval.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Loan $loan
     */
    protected function sendApprovalNotification($user, $loan)
    {
        try {
            if (empty($user->phone_number)) {
                Log::warning('User phone number missing for SMS notification', ['user_id' => $user->id, 'loan_id' => $loan->id]);
                return;
            }

            $sms = new SendSms();
            $message = "Habari {$user->first_name}, mkopo wako umeidhinishwa! Tutawasiliana nawe ndani ya siku 7 kwa ajili ya usakinishaji wa silinda. Tafadhali leta nyaraka zote za lazima unapokuja ofisini.";
            $success = $sms->send($user->id, $message);

            if ($success) {
                Log::info('SMS notification sent successfully', ['user_id' => $user->id, 'loan_id' => $loan->id]);
            } else {
                Log::error('Failed to send SMS notification', ['user_id' => $user->id, 'loan_id' => $loan->id]);
            }
        } catch (\Exception $e) {
            Log::error('Error sending SMS notification: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'loan_id' => $loan->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.loan-approval', [
            'cylinders' => CylinderType::all(),
        ]);
    }
}

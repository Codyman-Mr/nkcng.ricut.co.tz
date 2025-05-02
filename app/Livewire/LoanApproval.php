<?php

namespace App\Livewire;

use Livewire\Component;
use APP\Models\Loan;
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

    // public function approveLoan()
    // {

    //     Log::info('Loan Approval started');
    //     Log::info('Validating values');
    //     $this->validate([
    //         'cylinder_type' => 'required|exists:cylinder_types,id',
    //         'loan_required_amount' => 'required|string',
    //         'loan_payment_plan' => 'required|string',
    //         'loan_end_date' => [
    //             'required',
    //             'date',
    //             'after:' . now()->addYear()->format('Y-m-d'), // Must be at least 1 year from today
    //         ],
    //     ]);
    //     Log::info('Validation passed');

    //     DB::beginTransaction();
    //     Log::info('Database transaction started');

    //     try {
    //         Log::info('started approving loan');
    //         Log::info('started updating loan values in database');
    //         $this->loan->update([
    //             'loan_required_amount' => str_replace(',', '', $this->loan_required_amount),
    //             'loan_payment_plan' => $this->loan_payment_plan,
    //             'loan_end_date' => $this->loan_end_date,
    //             'status' => 'approved',
    //         ]);


    //         Log::info('Updating installation cylinder values');
    //         $this->loan->installation->update([
    //             'cylinder_type_id' => $this->cylinder_type,
    //         ]);

    //         DB::commit();
    //         Log::info('Database transaction committed');

    //         session()->flash('message', 'Loan approved successfully.');
    //         Log::info('Redirecting to home');
    //         return $this->redirect('/');
    //     } catch (\Exception $e) {
    //         session()->flash('error', $e->getMessage());
    //         Log::error('Error occurred: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    //         DB::rollback();
    //         session()->flash('error', 'An error occurred while submitting the application.');
    //     }
    //     if (config('app.debug')) {
    //         Log::debug('Query log', DB::getQueryLog());
    //     }
    // }


    public function approveLoan()
    {
        Log::info('Loan Approval started', ['loan_id' => $this->loan->id]);
        Log::info('Validating values', [
            'cylinder_type' => $this->cylinder_type,
            'loan_required_amount' => $this->loan_required_amount,
            'loan_payment_plan' => $this->loan_payment_plan,
            'loan_end_date' => $this->loan_end_date,
        ]);

        $this->validate([
            'cylinder_type' => 'required|exists:cylinder_types,id',
            'loan_required_amount' => 'required|string',
            'loan_payment_plan' => 'required|string',
            'loan_end_date' => [
                'required',
                'date',
                'after:' . now()->addYear()->format('Y-m-d'),
            ],
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
        $this->validate([
            'rejection_reason' => 'required|string|min:5'
        ]);

        $updated = $this->loan->update([
            'status' => 'rejected',
            'rejection_reason' => $this->rejection_reason
        ]);
        Log::info('Loan rejection update', ['updated' => $updated]);

        $this->showRejectModal = false;
        session()->flash('message', 'Loan rejected.');
        return $this->redirect('/');
    }

    public function render()
    {
        return view('livewire.loan-approval', [
            'cylinders' => CylinderType::all()
        ]);
    }
}

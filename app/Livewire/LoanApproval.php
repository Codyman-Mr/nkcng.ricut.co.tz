<?php

namespace App\Livewire;

use Livewire\Component;
use APP\Models\Loan;
use App\Models\CylinderType;

class LoanApproval extends Component
{
    public Loan $loan;
    public $cylinder_type, $loan_required_amount, $loan_payment_plan;
    public $showRejectModal = false;
    public $rejection_reason = '';

    public function mount(Loan $loan)
    {
        $this->loan = $loan->load(['user', 'documents', 'installation.customerVehicle']);
        $this->loan_required_amount = number_format($loan->loan_required_amount);
        $this->loan_payment_plan = $loan->loan_payment_plan;
        $this->cylinder_type = optional($loan->installation)->cylinder_type_id;
    }

    public function approveLoan()
    {

        // dd('approveLoan called');

        $this->validate([
            'cylinder_type' => 'required|exists:cylinder_types,id',
            'loan_required_amount' => 'required|string',
            'loan_payment_plan' => 'required|string',
        ]);

        try {
            $this->loan->update([
                'loan_required_amount' => str_replace(',', '', $this->loan_required_amount),
                'loan_payment_plan' => $this->loan_payment_plan,
                'status' => 'approved',
            ]);

            $this->loan->installation->update([
                'cylinder_type_id' => $this->cylinder_type,
            ]);

            session()->flash('message', 'Loan approved successfully.');
            return $this->redirect('/');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
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

        $this->loan->update([
            'status' => 'rejected',
            'rejection_reason' => $this->rejection_reason
        ]);

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

<?php

namespace App\Livewire;

use App\Models\Loan;
use App\Models\Payment;
use App\Models\Installation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ReportsComponent extends Component
{
    public $startDate;
    public $endDate;
    public $loanType = 'all';

    public function render()
    {

         
        // Fetch data based on filters
        $loans = Loan::query()
            ->when($this->loanType !== 'all', function ($query) {
                $query->where('loan_type', $this->loanType);
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
            })
            ->get();

        $payments = Payment::query()
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('payment_date', [$this->startDate, $this->endDate]);
            })
            ->get();

        $installations = Installation::query()
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
            })
            ->get();

        $users = User::query()
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
            })
            ->get();

        // Total Loan Amount & Total Paid
       $filteredLoansQuery = Loan::query()
    ->when($this->loanType !== 'all', function ($query) {
        $query->where('loan_type', $this->loanType);
    })
    ->when($this->startDate && $this->endDate, function ($query) {
        $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
    });

$totalLoanAmount = $filteredLoansQuery->sum('loan_required_amount');
$totalCustomers = (clone $filteredLoansQuery)->distinct('user_id')->count('user_id');

$totalPaidAmount = Payment::query()
    ->when($this->startDate && $this->endDate, function ($query) {
        $query->whereBetween('payment_date', [$this->startDate, $this->endDate]);
    })
    ->sum('paid_amount');


        // Loan status
        $loanStatus = [
            'active' => Loan::leftJoin(table: 'payments', first: 'loans.id', operator: '=', second: 'payments.loan_id')
                ->selectRaw(expression: 'loans.id, loans.loan_required_amount, COALESCE(SUM(payments.paid_amount), 0) as total_paid')
                ->groupBy('loans.id', 'loans.loan_required_amount')
                ->havingRaw(sql: 'total_paid > 0 AND total_paid < loans.loan_required_amount')
                ->count(),

            'repaid' => Loan::leftJoin(table: 'payments', first: 'loans.id', operator: '=', second: 'payments.loan_id')
                ->selectRaw('loans.id, loans.loan_required_amount, COALESCE(SUM(payments.paid_amount), 0) as total_paid')
                ->groupBy('loans.id', 'loans.loan_required_amount')
                ->havingRaw('total_paid >= loans.loan_required_amount')
                ->count(),

            'overdue' => Loan::leftJoin('payments', 'loans.id', '=', 'payments.loan_id')
                ->selectRaw('loans.id, loans.loan_required_amount, COALESCE(SUM(payments.paid_amount), 0) as total_paid, loans.loan_end_date')
                ->groupBy('loans.id', 'loans.loan_required_amount', 'loans.loan_end_date')
                ->havingRaw('total_paid < loans.loan_required_amount AND loans.loan_end_date < NOW()')
                ->count(),
        ];

        // Fetch metrics
        $metrics = [
            'loanStatus' => $loanStatus,
            'monthlyRepayments' => Payment::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(paid_amount) as total')
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->get(),
            'loanDistribution' => [
                'small' => Loan::where('loan_required_amount', '<', 1000)->count(),
                'medium' => Loan::whereBetween('loan_required_amount', [1000, 5000])->count(),
                'large' => Loan::where('loan_required_amount', '>', 5000)->count(),
            ],
            'defaultRate' => ($totalLoanAmount - $totalPaidAmount) / max($totalLoanAmount, 1) * 100,
        ];

        // New: List of customers with outstanding loans (madeni)
        $customersWithDebt = Loan::with('user', 'payments')
            ->get()
            ->map(function ($loan) {
                $paid = $loan->payments->sum('paid_amount');
                $remaining = $loan->loan_required_amount - $paid;
                return [
                    'customer_name' => $loan->user->name ?? 'N/A',
                    'loan_required_amount' => $loan->loan_required_amount,
                    'total_paid' => $paid,
                    'outstanding_amount' => $remaining > 0 ? $remaining : 0,
                ];
            })
            ->filter(fn($loan) => $loan['outstanding_amount'] > 0)
            ->values();

        return view('livewire.reports-component', [
            'loans' => $loans,
            'payments' => $payments,
            'installations' => $installations,
            'users' => $users,
            'metrics' => $metrics,
            'customersWithDebt' => $customersWithDebt,
            'totalLoanAmount' => $totalLoanAmount,
            'totalPaidAmount' => $totalPaidAmount,
            'totalLoans' => $loans->count(),
            'totalCustomers' => $users->count(),
        ]);
    }
}

<?php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\Installation;
use App\Models\User;

class ReportsComponent extends Component
{
    public $startDate;
    public $endDate;
    public $loanType = 'all';


    public function render()
    {
        // Fetch data based on filters
        // $loans = Loan::query()
        //     ->when($this->loanType !== 'all', function ($query) {
        //         $query->where('loan_type', $this->loanType);
        //     })
        //     ->when($this->startDate && $this->endDate, function ($query) {
        //         $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
        //     })
        //     ->get();

        $loans = Loan::select('id', 'loan_type', 'status', 'amount')
            ->when($this->loanType !== 'all', fn($query) => $query->where('loan_type', $this->loanType))
            ->when($this->startDate && $this->endDate, fn($query) => $query->whereBetween('created_at', [$this->startDate, $this->endDate]))
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


        // Fetch metrics
        $metrics = [
            'loanStatus' => [
                'active' => Loan::where('status', 'active')->count(),
                'repaid' => Loan::where('status', 'repaid')->count(),
                'overdue' => Loan::where('status', 'overdue')->count(),
            ],
            'monthlyRepayments' => Payment::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total')
                ->groupBy('year', 'month')
                ->orderBy('year', 'month')
                ->get(),
            'loanDistribution' => [
                'small' => Loan::where('amount', '<', 1000)->count(),
                'medium' => Loan::whereBetween('amount', [1000, 5000])->count(),
                'large' => Loan::where('amount', '>', 5000)->count(),
            ],
            'defaultRate' => Loan::where('status', 'overdue')->count() / max(Loan::count(), 1) * 100,
        ];

        return view('livewire.test', [
            'loans' => $loans,
            'payments' => $payments,
            'installations' => $installations,
            'users' => $users,
            'metrics' => $metrics,
        ]);

    }
}

<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\User;
use App\Models\Installation;
use Illuminate\Database\Eloquent;
use Carbon\Carbon;

class TestingComponent extends Component
{
    public $startDate;
    public $endDate;
    public $loanType = 'all';

    protected $dates = ['loan_start_date'];

    protected $queryString = [
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'loanType' => ['except' => 'all']
    ];

    public function mount()
    {
        $this->startDate = now()->subMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render()
{
    // Query ya loans ikiwa na filters za date na loan type, tumebadilisha loan_start_date -> created_at
    $filteredLoansQuery = Loan::with(['installation.cylinderType'])
        ->when($this->startDate && $this->endDate, fn($q) =>
            $q->whereBetween('created_at', [$this->startDate, $this->endDate]))
        ->when($this->loanType !== 'all', fn($q) =>
            $q->where('loan_type', $this->loanType));

    $loans = $filteredLoansQuery->get();

    // Filter ya ziada kuanzia Feb 2024 kwa loan_start_date (tunaangalia loan_start_date si created_at)
    $filteredLoans = $loans->filter(function ($loan) {
        // Kwa sababu loan_start_date inaweza kuwa null, tumhakikishie kwanza
        if (!$loan->loan_start_date) {
            return false;
        }
        $loanDate = Carbon::parse($loan->loan_start_date);
        return $loanDate->gte(Carbon::create(2024, 2, 1)) && $loanDate->lte(Carbon::now());
    });

    // Group by year-month kwa loan trend chart, tukitumaini loan_start_date ipo
    $loanTrends = $filteredLoans->groupBy(function ($loan) {
        return Carbon::parse($loan->loan_start_date)->format('Y-m');
    })->map(fn($group) => $group->count());

    $timePeriods = $loanTrends->keys()->toArray();
    $loanCounts = $loanTrends->values()->toArray();

    // Payments za loans filtered kwa date na loan_type
    $filteredPayments = Payment::whereBetween('payment_date', [$this->startDate, $this->endDate])
        ->when($this->loanType !== 'all', 
        
        
        
        function ($q) {
            $q->whereIn('loan_id', Loan::where('loan_type', $this->loanType)->pluck('id'));
        });

    // Payment methods for bar chart
    $paymentMethods = $filteredPayments->selectRaw('payment_method, sum(paid_amount) as total')
        ->groupBy('payment_method')
        ->get();

    $paymentMethodsTotals = $paymentMethods->pluck('total')->map(fn($v) => (float) $v)->toArray();
    $paymentMethodsLabels = $paymentMethods->pluck('payment_method')->toArray();

    // Payment methods for pie chart
    $paymentMethodsPie = $filteredPayments->selectRaw('payment_method, sum(paid_amount) as total')
        ->groupBy('payment_method')
        ->get();

    $paymentMethodsPieTotals = $paymentMethodsPie->pluck('total')->map(fn($v) => (float) $v)->toArray();
    $paymentMethodsPieLabels = $paymentMethodsPie->pluck('payment_method')->toArray();

    // Installations filtered kwa date
    $installations = Installation::with('cylinderType')
        ->whereBetween('created_at', [$this->startDate, $this->endDate])
        ->get()
        ->groupBy('cylinderType.name');

    $installationCounts = array_values($installations->mapWithKeys(fn($group, $key) => [$key => $group->count()])->toArray());
    $installationKeys = $installations->keys()->toArray();

    $reportData = [
        'totalLoans' => $loans->count(),
        'totalAmount' => $loans->sum('loan_required_amount'),
        'totalPaid' => $filteredPayments->sum('paid_amount'),
        'customers' => Loan::count(), // Jumla ya loan records (customers)
        'installations' => $installations,
        'paymentMethods' => $paymentMethods,
        'loanTrends' => $loanCounts,
        'timePeriods' => $timePeriods,
        'paymentMethodsTotals' => $paymentMethodsTotals,
        'paymentMethodsLabels' => $paymentMethodsLabels,
        'installationCounts' => $installationCounts,
        'installationKeys' => $installationKeys,
        'paymentMethodsPie' => $paymentMethodsPie,
        'paymentMethodsTotalPie' => $paymentMethodsPieTotals,
        'paymentMethodsPieLabels' => $paymentMethodsPieLabels,
        'filteredLoans' => $filteredLoans,
    ];

    return view('livewire.testing-component', $reportData);
}
}
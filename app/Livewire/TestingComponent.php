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
        $loans = Loan::with(['user', 'installation.cylinderType'])
            ->when($this->startDate && $this->endDate, fn($q) =>
                $q->whereBetween('loan_start_date', [$this->startDate, $this->endDate]))
            ->when($this->loanType !== 'all', fn($q) =>
                $q->where('loan_type', $this->loanType))
            ->get();

        // Group loans by month for a line chart
        // $loanTrends = $loans->groupBy(function ($loan) {
        //     return Carbon::parse($loan->loan_start_date)->format('Y-m');
        // })->map(fn($group) => $group->count());

        // $timePeriods = $loanTrends->keys()->toArray();
        // $loanCounts = $loanTrends->values()->toArray();


        // Filter loans to include only those starting from February 2024 to the present
        $filteredLoans = $loans->filter(function ($loan) {
            $loanDate = Carbon::parse($loan->loan_start_date);
            return $loanDate->gte(Carbon::create(2024, 2, 1)) && $loanDate->lte(Carbon::now());
        });

        // Group the filtered loans by year-month and count the loans in each group
        $loanTrends = $filteredLoans->groupBy(function ($loan) {
            return Carbon::parse($loan->loan_start_date)->format('Y-m');
        })->map(fn($group) => $group->count());

        // Extract the time periods (e.g., "2024-02", "2024-03", etc.) and the corresponding loan counts
        $timePeriods = $loanTrends->keys()->toArray();
        $loanCounts = $loanTrends->values()->toArray();



        // Payment methods data
        $paymentMethods = Payment::whereBetween('payment_date', [$this->startDate, $this->endDate])
            ->selectRaw('payment_method, sum(paid_amount) as total')
            ->groupBy('payment_method')
            ->get();

        $paymentMethodsTotals = $paymentMethods->pluck('total')->map(function ($value) {
            return (float) $value; // Convert to float
        })->toArray();
        $paymentMethodsLabels = $paymentMethods->pluck('payment_method')->toArray();


        // payment methods for pie chart
        $paymentMethodsPie = Payment::selectRaw('payment_method, sum(paid_amount) as total')
            ->groupBy('payment_method')
            ->get();

        $paymentMethodsPieTotals = $paymentMethodsPie->pluck('total')->map(function ($value) {
            return (float) $value; // Convert to float
        })->toArray();

        $paymentMethodsPieLabels = $paymentMethodsPie->pluck('payment_method')->toArray();


        // Extract labels
        $installations = Installation::with('cylinderType')
            ->get()
            ->groupBy('cylinderType.name');

        $installationCounts = array_values($installations->mapWithKeys(fn($group, $key) => [$key => $group->count()])->toArray());


        $installationKeys = $installations->keys()->toArray();


        $reportData = [
            'totalLoans' => $loans->count(),
            'totalAmount' => $loans->sum('loan_required_amount'),
            'totalPaid' => Payment::whereIn('loan_id', $loans->pluck('id'))
                ->sum('paid_amount'),
            'customers' => User::where('role', 'customer')->count(),
            'installations' => Installation::with('cylinderType')
                ->whereBetween('created_at', [$this->startDate, $this->endDate])
                ->get()
                ->groupBy('cylinderType.name'),
            'paymentMethods' => $paymentMethods,
            'loanTrends' => $loanCounts,
            'timePeriods' => $timePeriods,
            'paymentMethodsTotals' => $paymentMethodsTotals, // Pass totals
            'paymentMethodsLabels' => $paymentMethodsLabels, // Pass labels
            'installationCounts' => $installationCounts,
            'installationKeys' => $installationKeys,
            'paymentMethodsPie'=>$paymentMethodsPie,
            'paymentMethodsTotalPie'=>$paymentMethodsPieTotals,
            'paymentMethodsPieLabels'=>$paymentMethodsPieLabels,
            'filteredLoans' =>$filteredLoans
        ];

        return view('livewire.testing-component', $reportData);
    }
}

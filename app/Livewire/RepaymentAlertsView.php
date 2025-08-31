<?php



// class RepaymentAlertsView extends Component
// {
//     public $paymentPlan = '';
//     public $filterDate;
//     public $reminderType = '';

//     public function mount()
//     {
//         $this->filterDate = Carbon::now()->format('Y-m-d');
//     }

//     public function getUpcomingRepaymentsProperty()
//     {
//         return Loan::with('customer', 'payments')
//             ->when($this->paymentPlan, fn($q) => $q->where('payment_plan', $this->paymentPlan))
//             ->whereDate('due_date', '>=', Carbon::today())
//             ->whereDate('due_date', '<=', Carbon::today()->addDays(7))
//             ->get()
//             ->map(function ($loan) {
//                 $loan->amount_due = $loan->amount_to_finance - $loan->payments->sum('paid_amount');
//                 $loan->due_today = $loan->due_date->isToday();
//                 $loan->reminders = $loan->reminderLogs->groupBy('type')->map->first();
//                 return $loan;
//             });
//     }

//     public function getMissedRepaymentsProperty()
//     {
//         return Loan::with('customer', 'payments', 'reminderLogs')
//             ->when($this->paymentPlan, fn($q) => $q->where('payment_plan', $this->paymentPlan))
//             ->whereDate('due_date', '<', Carbon::today())
//             ->get()
//             ->filter(fn($loan) => $loan->amount_to_finance - $loan->payments->sum('paid_amount') > 0)
//             ->map(function ($loan) {
//                 $loan->amount_due = $loan->amount_to_finance - $loan->payments->sum('paid_amount');
//                 $loan->days_overdue = $loan->due_date->diffInDays(Carbon::today());
//                 $loan->reminder_after = $loan->reminderLogs->firstWhere('type', 'after');
//                 return $loan;
//             });
//     }

//     public function getReminderLogsProperty()
//     {
//         return RepaymentReminderLog::with('loan.customer')
//             ->when($this->reminderType, fn($q) => $q->where('type', $this->reminderType))
//             ->whereDate('created_at', $this->filterDate)
//             ->orderByDesc('created_at')
//             ->take(100)
//             ->get();
//     }

//     public function render()
//     {
//         return view('livewire.repayment-alerts-view', [
//             'upcomingRepayments' => $this->upcomingRepayments,
//             'missedRepayments' => $this->missedRepayments,
//             'reminderLogs' => $this->reminderLogs,
//         ]);
//     }
// } -->



namespace App\Livewire;

use App\Models\SmsLog;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class RepaymentAlertsView extends Component
{
    public $paymentPlan = '';
    public $filterDate;
    public $reminderType = '';
    public $search = '';

    public function mount()
    {
        $this->filterDate = Carbon::now()->format('Y-m-d');
    }

    public function getUpcomingRepaymentsProperty()
    {
        return Loan::with(['user', 'payments', 'reminderLogs', 'loanPackage'])
            ->when($this->paymentPlan, fn($q) => $q->where('loan_payment_plan', $this->paymentPlan))
            ->get()
            ->filter(function ($loan) {
                $dueDate = $loan->due_date;
                if (!$dueDate) {
                    Log::warning('Loan missing due_date', ['loan_id' => $loan->id]);
                    return false;
                }
                $isDueSoon = $dueDate->isBetween(Carbon::today(), Carbon::today()->addDays(7));
                $hasOutstanding = ($loan->loan_required_amount - $loan->payments->sum('paid_amount')) > 0;
                if (!$loan->loanPackage) {
                    Log::warning('Loan missing loanPackage', ['loan_id' => $loan->id]);
                }
                return $isDueSoon && $hasOutstanding;
            })
            ->filter(function ($loan) {
                return $this->search === '' || str_contains(strtolower($loan->user->formal_name), strtolower($this->search));
            })
            ->map(function ($loan) {
                $loan->amount_due = $loan->loan_required_amount - $loan->payments->sum('paid_amount');
                $loan->reminders = $loan->reminderLogs->groupBy('type')->map->first();
                return $loan;
            });
    }

    public function getMissedRepaymentsProperty()
    {
        return Loan::with(['user', 'payments', 'reminderLogs', 'loanPackage'])
            ->when($this->paymentPlan, fn($q) => $q->where('loan_payment_plan', $this->paymentPlan))
            ->get()
            ->filter(function ($loan) {
                $dueDate = $loan->due_date;
                if (!$dueDate) {
                    Log::warning('Loan missing due_date', ['loan_id' => $loan->id]);
                    return false;
                }
                $isOverdue = $dueDate->lt(Carbon::today());
                $hasOutstanding = ($loan->loan_required_amount - $loan->payments->sum('paid_amount')) > 0;
                if (!$loan->loanPackage) {
                    Log::warning('Loan missing loanPackage', ['loan_id' => $loan->id]);
                }
                return $isOverdue && $hasOutstanding;
            })
            ->map(function ($loan) {
                $loan->amount_due = $loan->loan_required_amount - $loan->payments->sum('paid_amount');
                $loan->days_overdue = $loan->due_date->diffInDays(Carbon::today());
                $loan->reminder_after = $loan->reminderLogs->firstWhere('type', 'after');
                return $loan;
            });
    }

    public function getReminderLogsProperty()
    {
        return SmsLog::query()
            ->whereDate('sent_at', $this->filterDate)
            ->orderByDesc('sent_at')
            ->take(100)
            ->get();
    }

    public function render()
    {
        return view('livewire.repayment-alerts-view', [
            'upcomingRepayments' => $this->upcomingRepayments,
            'missedRepayments' => $this->missedRepayments,
            'sms_logs' => $this->reminderLogs, 
        ]);
    }
}

<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use Carbon\CarbonInterval;

class LoanTableComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = '';
    public $sortDirection = 'asc';

    public $NextPaymentDate;

    protected $updatesQueryString = ['search', 'sortField', 'sortDirection'];

    protected $listeners = ['confirm-send-reminder' => 'sendReminder'];


    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetSorting()
    {
        $this->sortField = '';
        $this->sortDirection = 'asc';
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function render()
    {
        $payments = Payment::with('loan')->get();
        $user = Auth::user();
        $totalLoanAmount = Loan::sum('loan_required_amount');
        $users = User::with('loans.payments')->get();

        $nearEndLoans = Loan::whereNotNull('loan_end_date')
            ->whereDate('loan_end_date', '>', now())
            ->orderBy('loan_end_date', 'asc')
            ->take(12)
            ->with('user')
            ->get();

        // Fetch and compute custom values
        $loans = Loan::with(['user', 'payments'])
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%");
                });
            })
            ->get()
            ->map(function ($loan) {
                $paid = $loan->payments->sum('paid_amount');
                $remaining = $loan->loan_required_amount - $paid;

                $loan->amount_paid = $paid;
                $loan->amount_remaining = $remaining;

                $loan->days_remaining = $loan->loan_end_date
                    ? now()->diffInDays($loan->loan_end_date, false)
                    : null;

                return $loan;
            });

        // Sort the results
        if ($this->sortField) {
            $loans = $loans->sortBy($this->sortField, SORT_REGULAR, $this->sortDirection === 'desc');
        }

        // Paginate the collection
        $paginatedLoans = $this->paginateCollection($loans, 10);
        $today = Carbon::now();
        return view('livewire.loan-table-component', [
            'loans' => $paginatedLoans,
            'payments' => $payments,
            'user' => $user,
            'users' => $users,
            'totalLoanAmount' => $totalLoanAmount,
            'nearEndLoans' => $nearEndLoans,
            'today' => $today,
        ]);
    }

    /**
     * Manual collection pagination
     */
    public function paginateCollection($items, $perPage = 10)
    {
        $page = request()->get('page', 1);
        $items = collect($items);
        $pagedItems = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator($pagedItems, $items->count(), $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
    }

    protected function convertPhoneNumberToInternationalFormat(string $phoneNumber)
    {
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);

        if (preg_match('/^0(6|7|8)\d{8}$/', $phoneNumber)) {
            return '+255' . substr($phoneNumber, 1);
        }

        if (preg_match('/^255(6|7|8)\d{8}$/', $phoneNumber)) {
            return '+' . $phoneNumber;
        }

        return $phoneNumber;
    }

     protected function calculateNextDueDate($loan)
    {
        $lastPaymentDate = $loan->payments->max('payment_date')
            ? Carbon::parse($loan->payments->max('payment_date'))
            : Carbon::parse($loan->created_at);

        return match ($loan->payment_plan) {
            'weekly' => $lastPaymentDate->copy()->addWeek(),
            'monthly' => $lastPaymentDate->copy()->addMonth(),
            default => $lastPaymentDate->copy()->addWeek(),
        };
    }

    public function sendReminder($loanId)
    {
        $loan = Loan::with(['user', 'payments'])->findOrFail($loanId);

        try {
            $phoneNumber = $this->convertPhoneNumberToInternationalFormat($loan->user->phone_number);
            $nextDueDate = $this->calculateNextDueDate($loan);
            $formattedDate = $nextDueDate->format('d/m/Y');

            $message = "Habari {$loan->user->first_name}, "
                . "Kumbukumbu ya malipo yako ijayo ni tarehe $formattedDate. "
                . "Tafadhali hakikisha unalipa kwa wakati ili kuepuka malipo ya ziada.";

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'apiKey' => config('services.africastalking.api_key'),
            ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
                        'username' => config('services.africastalking.username'),
                        'to' => $phoneNumber,
                        'from' => 'NK CNG',
                        'message' => $message,
                        'enqueue' => 1,
                    ]);

            if ($response->successful()) {
                Log::info('sms sent succesfully to'. $loan->user->firstname + $phoneNumber);
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => "Ujumbe umetumwa kwa {$loan->user->first_name} ({$phoneNumber})"
                ]);
                return true;
            }

            throw new \Exception("SMS API request failed: " . $response->body());
        } catch (\Exception $e) {
            Log::error("sendReminder failed: " . $e->getMessage());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Tatizo limetokea wakati wa kutuma ujumbe: ' . $e->getMessage()
            ]);

        }
    }

}

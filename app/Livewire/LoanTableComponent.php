<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Jobs\InitiatePaymentJob;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Jobs\SendSmsJob;
use Illuminate\Support\Str;

use function Termwind\render;

class LoanTableComponent extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $sortField = '';
    public $sortDirection = 'asc';
    public $selectedLoanId;
    public $phoneNumber;
    public $bankName;
    public $provider;
    public $paymentAmount;
    public $paymentMethod = 'cash';
    public $receipt;
    public $showPaymentModal = false;
    public $isLoading = false;

    protected $updatesQueryString = ['search', 'sortField', 'sortDirection'];
    protected $listeners = ['confirm-send-reminder' => 'sendReminder'];

    public function rules()
    {
       return [
    'paymentAmount' => 'required|numeric|min:1000',
    'paymentMethod' => 'required|in:cash,mobile_money,bank',
    'phoneNumber' => ['required_if:paymentMethod,mobile_money', 'string', 'regex:/^(\+255|0)[0-9]{9}$/'],
    'provider' => 'required_if:paymentMethod,mobile_money|in:Mpesa,TigoPesa,AirtelMoney,HaloPesa',
    'bankName' => 'required_if:paymentMethod,bank',
    'receipt' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:10240',

        ];
    }

    public function openPaymentModal($loanId)
    {
        $this->selectedLoanId = $loanId;
        $this->showPaymentModal = true;
        $this->reset(['paymentMethod', 'paymentAmount', 'phoneNumber', 'provider', 'receipt', 'isLoading']);
        $this->paymentMethod = 'cash';
        $loan = Loan::find($loanId);
        if ($loan && $loan->user) {
            $this->phoneNumber = $loan->user->phone_number;
            $this->provider = 'Mpesa';
        }
        Log::info('Opened payment modal', ['loan_id' => $loanId]);
    }



    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->reset(['paymentMethod', 'paymentAmount', 'phoneNumber', 'provider', 'receipt', 'selectedLoanId', 'isLoading']);
        $this->resetValidation();
        Log::info('Closed payment modal');
    }

public function addPayment() 
{
    $this->validate();
    $this->isLoading = true;
    $this->dispatch('payment-processing'); // Debugging event

    Log::info('Adding payment', [
        'loan_id' => $this->selectedLoanId,
        'amount' => $this->paymentAmount,
        'phone_number' => $this->phoneNumber,
        'provider' => $this->provider,
    ]);

    try {
        $loan = Loan::findOrFail($this->selectedLoanId);
        if ($loan->status !== 'approved') {
            throw new \Exception('Payments can only be added for approved loans.');
        }

        if ($this->paymentMethod === 'mobile_money') {
            $normalizedPhoneNumber = $this->normalizePhoneNumber($this->phoneNumber);
            InitiatePaymentJob::dispatch($loan->id, $this->paymentAmount, $normalizedPhoneNumber, $this->provider);

           $transactionId = 'MM_' . $loan->id . '_' . Str::uuid();

Payment::create([
    'loan_id' => $loan->id,
    'user_id' => $loan->user->id,
    'paid_amount' => $this->paymentAmount,
    'payment_date' => now()->format('Y-m-d'),
    'transaction_id' => $transactionId,
    'external_id' => $transactionId, // tumia transactionId kama temporary external_id
    'status' => 'pending',
    'job_status' => 'processing',
    'payment_method' => 'mobile_money',
    'provider' => $this->provider,
    'receipt_path' => null,
    'created_at' => now(),
    'updated_at' => now(),
]);


            $recipients = [$normalizedPhoneNumber];
            $message = "Payment of TZS {$this->paymentAmount} for Loan #{$loan->id} has been initiated.";
            SendSmsJob::dispatch($recipients, $message, $loan->id);

            Log::info('Mobile money payment dispatched', [
                'loan_id' => $loan->id,
                'amount' => $this->paymentAmount,
                'phone_number' => $normalizedPhoneNumber,
                'provider' => $this->provider,
            ]);
        } elseif ($this->paymentMethod === 'cash') {
            $userName = str_replace(' ', '_', strtolower($loan->user->name ?? 'user_' . $loan->id));
            $timestamp = now()->format('YmdHis');
            Storage::disk('public')->makeDirectory('cash-receipts');

            $path = null;
            if ($this->receipt) {
                $extension = $this->receipt->getClientOriginalExtension();
                $fileName = "{$userName}_receipt_{$timestamp}.{$extension}";
                $path = $this->receipt->storeAs('cash-receipts', $fileName, 'public');

                $fileExists = Storage::disk('public')->exists($path);
                if (!$fileExists) {
                    throw new \Exception("Failed to store receipt: {$fileName}");
                }
            }

            $transactionId = 'CASH_' . $loan->id . '_' . Str::uuid();
            Payment::create([
                'loan_id' => $loan->id,
                'user_id' => $loan->user->id,
                'paid_amount' => $this->paymentAmount,
                'payment_date' => now()->format('Y-m-d'),
                'transaction_id' => $transactionId,
                'external_id' => $transactionId,
                'status' => 'pending',
                'job_status' => 'completed',
                'payment_method' => 'cash',
                'provider' => 'Cash', // hardcoded cash
                'receipt_path' => $path,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $recipients = [$this->normalizePhoneNumber($loan->user->phone_number)];
            $message = "Cash payment of TZS {$this->paymentAmount} for Loan #{$loan->id} recorded, pending verification.";
            SendSmsJob::dispatch($recipients, $message, $loan->id);

            Log::info('Cash payment recorded', [
                'loan_id' => $loan->id,
                'transaction_id' => $transactionId,
                'receipt_path' => $path,
            ]);
        } elseif ($this->paymentMethod === 'bank') {
            $transactionId = 'BANK_' . $loan->id . '_' . Str::uuid();

            // Hii ni sehemu muhimu: hakikisha provider ni provider halisi (benki) sio 'Mpesa' default
            $bankProvider = $this->provider;

            Payment::create([
                'loan_id' => $loan->id,
                'user_id' => $loan->user->id,
                'paid_amount' => $this->paymentAmount,
                'payment_date' => now()->format('Y-m-d'),
                'transaction_id' => $transactionId,
                'external_id' => $transactionId,
                'status' => 'pending',
                'job_status' => 'completed',
                'payment_method' => 'bank',
                'provider' => $bankProvider, // Hapa sasa provider ni benki halisi
                'receipt_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $recipients = [$this->normalizePhoneNumber($loan->user->phone_number)];
            $message = "Bank payment of TZS {$this->paymentAmount} for Loan #{$loan->id} recorded, pending verification.";
            SendSmsJob::dispatch($recipients, $message, $loan->id);

            Log::info('Bank payment recorded', [
                'loan_id' => $loan->id,
                'transaction_id' => $transactionId,
                'provider' => $bankProvider,
            ]);
        }

        // New addition: Send email notification after payment recorded/initiated
        if (isset($loan->user->email)) {
            \Mail::to($loan->user->email)->send(new \App\Mail\PaymentNotificationMail($loan, $this->paymentAmount, $this->paymentMethod));
            Log::info('Payment notification email sent to user', ['email' => $loan->user->email]);
        }

        session()->flash('message', $this->paymentMethod === 'cash'
            ? 'Cash payment recorded successfully.'
            : 'Payment initiated successfully. You will be notified once processed.');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => session()->get('message'),
        ]);
        $this->closePaymentModal();
    } catch (\Exception $e) {
        Log::error('Failed to add payment: ' . $e->getMessage(), [
            'loan_id' => $this->selectedLoanId,
            'trace' => $e->getTraceAsString(),
        ]);
        session()->flash('error', 'Failed to add payment: ' . $e->getMessage());
        $this->dispatch('notify', [
            'type' => 'error',
            'message' => 'Failed to add payment: ' . $e->getMessage(),
        ]);
    } finally {
        $this->isLoading = false;
        $this->dispatch('payment-processed');
    }
}



    protected function normalizePhoneNumber(string $phoneNumber): string
    {
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);
        if (preg_match('/^0(6|7|8)\d{8}$/', $phoneNumber)) {
            return '+255' . substr($phoneNumber, 1);
        }
        if (preg_match('/^(?:\+?255)(6|7|8)\d{8}$/', $phoneNumber)) {
            return '+' . ltrim($phoneNumber, '+');
        }
        return $phoneNumber;
    }

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

  $loans = Loan::with(['user', 'payments'])
    ->where('status', 'approved') // Tumia status kutoka kwenye loan table
    ->when($this->search, function ($query) {
        $query->where('applicant_name', 'like', "%{$this->search}%");
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

        if ($this->sortField) {
            $loans = $loans->sortBy($this->sortField, SORT_REGULAR, $this->sortDirection === 'desc');
        }

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
                Log::info('SMS sent successfully to ' . $loan->user->first_name . ' at ' . $phoneNumber);
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


<?php

// namespace App\Livewire;

// use App\Jobs\InitiatePaymentJob;
// use App\Jobs\SendSmsJob;
// use App\Models\Installation;
// use App\Models\Loan;
// use App\Models\Payment;
// use App\Events\PaymentStatusUpdated;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;
// use Livewire\Component;
// use App\Models\CylinderType;

// class LoanApproval extends Component
// {
//     public $loan;
//     public $loanId;
//     public $cylinderType;
//     public $loanRequiredAmount;
//     public $loanPaymentPlan;
//     public $loanEndDate;
//     public $paymentAmount;
//     public $phoneNumber;
//     public $provider;
//     public $paymentStatus;
//     public $showRejectModal = false;
//     public $rejection_reason = '';
//     public $cylinders;

//     public $loanInstallationId;

//     public function rules()
//     {
//         return [
//             'cylinderType' => 'required|integer|exists:cylinder_types,id',
//             'loanRequiredAmount' => 'required|numeric|min:1000',
//             'loanPaymentPlan' => 'required|in:weekly,monthly,quarterly',
//             'loanEndDate' => 'required|date|after:today',
//             'paymentAmount' => 'required|numeric|min:1000',
//             'phoneNumber' => ['required', 'string', 'regex:/^(\+255|0)[0-9]{9}$/'],
//             'provider' => 'required|in:Mpesa,TigoPesa,AirtelMoney,HaloPesa',
//             'rejection_reason' => 'required_if:showRejectModal,true|string|min:5|max:1000',
//         ];
//     }

//     public function mount(Loan $loan)
//     {
//         $this->loan = $loan->load(['user', 'privateGuarantor', 'governmentGuarantor', 'installation']);
//         $this->loanId = $loan->id;
//         $this->paymentStatus = 'Not started';
//         $this->loanInstallationId = $loan->installation->id;
//         $this->cylinderType = optional($loan->installation)->cylinder_type_id;
//         $this->loanRequiredAmount = $loan->loan_required_amount;
//         $this->loanPaymentPlan = $loan->loan_payment_plan ?? 'weekly';
//         $this->loanEndDate = $loan->loan_end_date ? \Carbon\Carbon::parse($loan->loan_end_date)->format('Y-m-d') : null;
//         $this->phoneNumber = $loan->user->phone_number ?? null;
//         $this->paymentAmount = $loan->loan_required_amount ? $loan->loan_required_amount / 10 : null;
//         $this->provider = 'Mpesa';
//         $this->cylinders = CylinderType::all();
//     }

//     public function approveLoan()
//     {
//         $this->validate();

//         Log::info('Loan Approval started', ['loan_id' => $this->loanId]);
//         Log::info('Input values', [
//             'cylinder_type' => $this->cylinderType,
//             'loan_required_amount' => $this->loanRequiredAmount,
//             'loan_payment_plan' => $this->loanPaymentPlan,
//             'loan_end_date' => $this->loanEndDate,
//             'payment_amount' => $this->paymentAmount,
//             'phoneNumber' => $this->phoneNumber,
//             'provider' => $this->provider,
//         ]);

//         try {
//             DB::beginTransaction();
//             Log::info('Database transaction started');

//             $this->loan->update([
//                 'loan_required_amount' => $this->loanRequiredAmount,
//                 'loan_payment_plan' => $this->loanPaymentPlan,
//                 'loan_end_date' => $this->loanEndDate,
//                 'status' => 'approved',
//             ]);
//             Log::info('Loan update result', ['updated' => true]);

//             $installation = Installation::where('id', $this->loanInstallationId)->firstOrFail();
//             $installation->update(['cylinder_type_id' => $this->cylinderType]);
//             Log::info('Installation update result', ['updated' => true]);

//             DB::commit();
//             Log::info('Database transaction committed');

//             // Normalize phone number to +255 format
//             $normalizedPhone = $this->normalizePhoneNumber($this->phoneNumber);

//             // Dispatch payment job asynchronously
//             InitiatePaymentJob::dispatch($this->loanId, $this->paymentAmount, $normalizedPhone, $this->provider);
//             Log::info('Payment job dispatched', ['loan_id' => $this->loanId, 'phone' => $normalizedPhone]);

//             // Collect recipients (user, private guarantor, government guarantor)
//             $recipients = [];
//             if ($this->loan->user && $this->loan->user->phone_number) {
//                 $recipients[] = $this->normalizePhoneNumber($this->loan->user->phone_number);
//             }
//             if ($this->loan->privateGuarantor && $this->loan->privateGuarantor->phone_number) {
//                 $recipients[] = $this->normalizePhoneNumber($this->loan->privateGuarantor->phone_number);
//             }
//             if ($this->loan->governmentGuarantor && $this->loan->governmentGuarantor->phone_number) {
//                 $recipients[] = $this->normalizePhoneNumber($this->loan->governmentGuarantor->phone_number);
//             }

//             // Remove duplicates
//             $recipients = array_unique($recipients);

//             if (!empty($recipients)) {
//                 // Dispatch SMS job
//                 $message = "Loan #{$this->loanId} approved! Payment of TZS {$this->paymentAmount} is being processed.";
//                 SendSmsJob::dispatch($recipients, $message, $this->loanId);
//                 Log::info('SMS job dispatched', ['loan_id' => $this->loanId, 'recipients' => $recipients]);
//             } else {
//                 Log::warning('No valid recipients found for SMS', ['loan_id' => $this->loanId]);
//             }

//             session()->flash('message', 'Loan approval initiated. You will be notified once the payment is processed.');
//             return redirect()->to('/');
//         } catch (\Exception $e) {
//             DB::rollback();
//             Log::error('Loan approval error: ' . $e->getMessage(), ['loan_id' => $this->loanId, 'trace' => $e->getTraceAsString()]);
//             session()->flash('error', 'Failed to approve loan: ' . $e->getMessage());
//         }
//     }

//     public function checkPaymentStatus()
//     {
//         $payment = Payment::where('loan_id', $this->loanId)->latest()->first();
//         if ($payment) {
//             $this->paymentStatus = $payment->job_status;
//             if ($payment->job_status === 'completed') {
//                 session()->flash('message', 'Payment initiated successfully! Please complete the transaction on your phone.');
//             } elseif ($payment->job_status === 'failed') {
//                 session()->flash('error', 'Payment failed. Please try again or contact support.');
//             }
//         } else {
//             $this->paymentStatus = 'Not started';
//         }
//         Log::info('Payment status checked', ['loan_id' => $this->loanId, 'status' => $this->paymentStatus]);
//     }

//     protected function normalizePhoneNumber(string $phoneNumber): string
//     {
//         $phoneNumber = preg_replace('/\D/', '', $phoneNumber);
//         if (preg_match('/^0(6|7|8)\d{8}$/', $phoneNumber)) {
//             return '+255' . substr($phoneNumber, 1);
//         }
//         if (preg_match('/^(?:\+?255)(6|7|8)\d{8}$/', $phoneNumber)) {
//             return '+' . ltrim($phoneNumber, '+');
//         }
//         return $phoneNumber;
//     }

//     public function openRejectionModal()
//     {
//         $this->showRejectModal = true;
//     }

//     public function rejectLoan()
//     {
//         $this->validateOnly('rejection_reason');
//         Log::info('Rejection reason validated', ['loan_id' => $this->loanId, 'rejection_reason' => $this->rejection_reason]);

//         try {
//             DB::beginTransaction();
//             $this->loan->update([
//                 'status' => 'rejected',
//                 'rejection_reason' => $this->rejection_reason,
//             ]);
//             Log::info('Loan rejection updated', ['loan_id' => $this->loanId]);
//             DB::commit();
//             session()->flash('message', 'Loan rejected successfully.');
//             $this->showRejectModal = false;
//             $this->dispatch('notify', ['type' => 'success', 'message' => 'Loan rejected successfully.']);
//             return redirect()->route('users');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             Log::error('Loan rejection error: ' . $e->getMessage(), ['loan_id' => $this->loanId]);
//             session()->flash('error', 'Failed to reject loan: ' . $e->getMessage());
//         }
//     }

//     public function render()
//     {
//         return view('livewire.loan-approval', [
//             'cylinders' => $this->cylinders,
//         ]);
//     }
// }

namespace App\Livewire;

use App\Jobs\InitiatePaymentJob;
use App\Jobs\SendSmsJob;
use App\Models\Installation;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\LoanDocument;
use App\Models\User;
use App\Events\PaymentStatusUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\CylinderType;

class LoanApproval extends Component
{
    use WithFileUploads;

    public $loan;
    public $loanId;
    public $cylinderType;
    public $loanRequiredAmount;
    public $loanPaymentPlan;
    public $loanEndDate;
    public $paymentAmount;
    public $phoneNumber;
    public $provider;
    public $paymentStatus;
    public $showRejectModal = false;
    public $rejection_reason = '';
    public $cylinders;
    public $loanInstallationId;
    public $documents = [
        'mktaba_wa_mkopo' => null,
        'kitambulisho_mwomba_mbele' => null,
        'kitambulisho_mdhamini_1_mbele' => null,
        'kitambulisho_mdhamini_2_mbele' => null,
        'leseni_mwomba' => null,
        'kadi_ya_usafiri' => null,
        'barua_ya_utambulisho' => null,
    ];

    public function rules()
    {
        return [
            'cylinderType' => 'required|integer|exists:cylinder_types,id',
            'loanRequiredAmount' => 'required|numeric|min:1000',
            'loanPaymentPlan' => 'required|in:weekly,monthly,quarterly',
            'loanEndDate' => 'required|date|after:today',
            'paymentAmount' => 'required|numeric|min:1000',
            'phoneNumber' => ['required', 'string', 'regex:/^(\+255|0)[0-9]{9}$/'],
            'provider' => 'required|in:Mpesa,TigoPesa,AirtelMoney,HaloPesa',
            'rejection_reason' => 'required_if:showRejectModal,true|string|min:5|max:1000',
            'documents.mktaba_wa_mkopo' => 'nullable|file|mimes:pdf,png,jpg,jpeg,docx|max:10240',
            'documents.kitambulisho_mwomba_mbele' => 'nullable|file|mimes:pdf,png,jpg,jpeg,docx|max:10240',
            'documents.kitambulisho_mdhamini_1_mbele' => 'nullable|file|mimes:pdf,png,jpg,jpeg,docx|max:10240',
            'documents.kitambulisho_mdhamini_2_mbele' => 'nullable|file|mimes:pdf,png,jpg,jpeg,docx|max:10240',
            'documents.leseni_mwomba' => 'nullable|file|mimes:pdf,png,jpg,jpeg,docx|max:10240',
            'documents.kadi_ya_usafiri' => 'nullable|file|mimes:pdf,png,jpg,jpeg,docx|max:10240',
            'documents.barua_ya_utambulisho' => 'nullable|file|mimes:pdf,png,jpg,jpeg,docx|max:10240',
        ];
    }

    public function mount(Loan $loan)
    {
        $this->loan = $loan->load(['user', 'privateGuarantor', 'governmentGuarantor', 'installation']);
        $this->loanId = $loan->id;
        $this->paymentStatus = 'Not started';
        $this->loanInstallationId = $loan->installation->id;
        $this->cylinderType = optional($loan->installation)->cylinder_type_id;
        $this->loanRequiredAmount = $loan->loan_required_amount;
        $this->loanPaymentPlan = $loan->loan_payment_plan ?? 'weekly';
        $this->loanEndDate = $loan->loan_end_date ? \Carbon\Carbon::parse($loan->loan_end_date)->format('Y-m-d') : null;
        $this->phoneNumber = $loan->user->phone_number ?? null;
        $this->paymentAmount = $loan->loan_required_amount ? $loan->loan_required_amount / 10 : null;
        $this->provider = 'Mpesa';
        $this->cylinders = CylinderType::all();
        Log::info('LoanApproval component mounted', [
            'loan_id' => $this->loanId,
            'user_id' => $this->loan->user ? $this->loan->user->id : null,
            'has_user' => !is_null($this->loan->user),
        ]);
    }

    public function uploadDocuments()
    {
        $this->validateOnly('documents.*');
        Log::info('Document upload started', ['loan_id' => $this->loanId]);

        try {
            DB::beginTransaction();
            $userName = str_replace(' ', '_', strtolower($this->loan->user->name ?? 'user_' . $this->loanId));
            $timestamp = now()->format('YmdHis');

            Storage::disk('public')->makeDirectory('client-documents');
            $diskPath = Storage::disk('public')->path('');
            Log::info('Storage disk info', [
                'loan_id' => $this->loanId,
                'disk_path' => $diskPath,
                'directory_exists' => Storage::disk('public')->exists('client-documents'),
            ]);

            foreach ($this->documents as $key => $file) {
                if ($file) {
                    $extension = $file->getClientOriginalExtension();
                    $fileName = "{$userName}_{$key}_{$timestamp}.{$extension}";
                    $path = $file->storeAs('client-documents', $fileName, 'public');

                    $fileExists = Storage::disk('public')->exists($path);
                    $absolutePath = storage_path('app/public/' . $path);
                    Log::info('Document storage attempt', [
                        'loan_id' => $this->loanId,
                        'document_type' => $key,
                        'file_name' => $fileName,
                        'path' => $path,
                        'absolute_path' => $absolutePath,
                        'file_exists' => $fileExists,
                    ]);

                    if (!$fileExists) {
                        throw new \Exception("Failed to store file: {$fileName}");
                    }

                    LoanDocument::create([
                        'loan_id' => $this->loanId,
                        'document_type' => $key,
                        'document_path' => $path,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    Log::info('Document uploaded', [
                        'loan_id' => $this->loanId,
                        'document_type' => $key,
                        'path' => $path,
                    ]);
                }
            }

            DB::commit();
            session()->flash('message', 'Documents uploaded successfully.');
            $this->reset('documents');
            Log::info('Document upload completed', ['loan_id' => $this->loanId]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Document upload error: ' . $e->getMessage(), [
                'loan_id' => $this->loanId,
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('error', 'Failed to upload documents: ' . $e->getMessage());
        }
    }

    public function approveLoan()
    {
        $this->validate();

        Log::info('Loan Approval started', ['loan_id' => $this->loanId]);
        Log::info('Input values', [
            'cylinder_type' => $this->cylinderType,
            'loan_required_amount' => $this->loanRequiredAmount,
            'loan_payment_plan' => $this->loanPaymentPlan,
            'loan_end_date' => $this->loanEndDate,
            'payment_amount' => $this->paymentAmount,
            'phoneNumber' => $this->phoneNumber,
            'provider' => $this->provider,
        ]);

        try {
            DB::beginTransaction();
            Log::info('Database transaction started');

            // Check if all required documents are uploaded
            $requiredDocs = [
                'mktaba_wa_mkopo',
                'kitambulisho_mwomba_mbele',
                'kitambulisho_mdhamini_1_mbele',
                'kitambulisho_mdhamini_2_mbele',
                'leseni_mwomba',
                'kadi_ya_usafiri',
                'barua_ya_utambulisho',
            ];
            $uploadedDocs = LoanDocument::where('loan_id', $this->loanId)->pluck('document_type')->toArray();
            $missingDocs = array_diff($requiredDocs, $uploadedDocs);
            if (!empty($missingDocs)) {
                throw new \Exception('Missing required documents: ' . implode(', ', $missingDocs));
            }
            Log::info('Document validation passed', ['loan_id' => $this->loanId]);

            // Ensure loan has a valid user
            if (!$this->loan->user || !$this->loan->user->id) {
                throw new \Exception('No user associated with loan ID: ' . $this->loanId);
            }
            $user = User::find($this->loan->user->id);
            if (!$user) {
                throw new \Exception('User ID ' . $this->loan->user->id . ' does not exist in users table for loan ID: ' . $this->loanId);
            }
            Log::info('User validation passed', [
                'loan_id' => $this->loanId,
                'user_id' => $this->loan->user->id,
                'user_exists' => !is_null($user),
            ]);

            $this->loan->update([
                'loan_required_amount' => $this->loanRequiredAmount,
                'loan_payment_plan' => $this->loanPaymentPlan,
                'loan_end_date' => $this->loanEndDate,
                'status' => 'approved',
            ]);
            Log::info('Loan update result', ['updated' => true]);

            $installation = Installation::where('id', $this->loanInstallationId)->firstOrFail();
            $installation->update(['cylinder_type_id' => $this->cylinderType]);
            Log::info('Installation update result', ['updated' => true]);

            DB::commit();
            Log::info('Database transaction committed');

            $normalizedPhone = $this->normalizePhoneNumber($this->phoneNumber);

            Log::info('Initiating payment job', [
                'loan_id' => $this->loanId,
                'user_id' => $this->loan->user->id,
                'amount' => $this->paymentAmount,
                'phone_number' => $normalizedPhone,
                'provider' => $this->provider,
            ]);
            InitiatePaymentJob::dispatch($this->loanId, $this->paymentAmount, $normalizedPhone, $this->provider);

            $recipients = [];
            if ($this->loan->user && $this->loan->user->phone_number) {
                $recipients[] = $this->normalizePhoneNumber($this->loan->user->phone_number);
            }
            if ($this->loan->privateGuarantor && $this->loan->privateGuarantor->phone_number) {
                $recipients[] = $this->normalizePhoneNumber($this->loan->privateGuarantor->phone_number);
            }
            if ($this->loan->governmentGuarantor && $this->loan->governmentGuarantor->phone_number) {
                $recipients[] = $this->normalizePhoneNumber($this->loan->governmentGuarantor->phone_number);
            }

            $recipients = array_unique($recipients);

            if (!empty($recipients)) {
                $message = "Loan #{$this->loanId} approved! Payment of TZS {$this->paymentAmount} is being processed.";
                SendSmsJob::dispatch($recipients, $message, $this->loanId);
                Log::info('SMS job dispatched', ['loan_id' => $this->loanId, 'recipients' => $recipients]);
            } else {
                Log::warning('No valid recipients found for SMS', ['loan_id' => $this->loanId]);
            }

            session()->flash('message', 'Loan approval initiated. You will be notified once the payment is processed.');
            return redirect()->to('/');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Loan approval error: ' . $e->getMessage(), ['loan_id' => $this->loanId, 'trace' => $e->getTraceAsString()]);
            session()->flash('error', 'Failed to approve loan: ' . $e->getMessage());
        }
    }

    public function checkPaymentStatus()
    {
        $payment = Payment::where('loan_id', $this->loanId)->latest()->first();
        if ($payment) {
            $this->paymentStatus = $payment->job_status;
            if ($payment->job_status === 'completed') {
                session()->flash('message', 'Payment initiated successfully! Please complete the transaction on your phone.');
            } elseif ($payment->job_status === 'failed') {
                session()->flash('error', 'Payment failed. Please try again or contact support.');
            }
        } else {
            $this->paymentStatus = 'Not started';
        }
        Log::info('Payment status checked', ['loan_id' => $this->loanId, 'status' => $this->paymentStatus]);
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

    public function openRejectionModal()
    {
        $this->showRejectModal = true;
    }

    public function rejectLoan()
    {
        $this->validateOnly('rejection_reason');
        Log::info('Rejection reason validated', ['loan_id' => $this->loanId, 'rejection_reason' => $this->rejection_reason]);

        try {
            DB::beginTransaction();
            $this->loan->update([
                'status' => 'rejected',
                'rejection_reason' => $this->rejection_reason,
            ]);
            Log::info('Loan rejection updated', ['loan_id' => $this->loanId]);
            DB::commit();
            session()->flash('message', 'Loan rejected successfully.');
            $this->showRejectModal = false;
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Loan rejected successfully.']);
            return redirect()->route('users');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Loan rejection error: ' . $e->getMessage(), ['loan_id' => $this->loanId]);
            session()->flash('error', 'Failed to reject loan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.loan-approval', [
            'cylinders' => $this->cylinders,
        ]);
    }
}

<?php

namespace App\Livewire;

use App\Jobs\InitiatePaymentJob;
use App\Models\Installation;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\LoanDocument;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\CylinderType;
use Illuminate\Support\Str;

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
    public $paymentMethod = 'cash';
    public $receipt;
    public $loan_start_date;

    public $documents = [
        'mktaba_wa_mkopo' => null,
        'kitambulisho_mwomba_mbele' => null,
        'kitambulisho_mdhamini_1_mbele' => null,
        'kitambulisho_mdhamini_2_mbele' => null,
        'leseni_mwomba' => null,
        'kadi_ya_usafiri' => null,
        'barua_ya_utambulisho' => null,
    ];
    public $uploadedDocumentTypes = [];

    public function rules()
    {
        $rules = [
            'cylinderType' => 'required|integer|exists:cylinder_types,id',
            'loanRequiredAmount' => 'required|numeric|min:1000',
            'loanPaymentPlan' => 'required|in:weekly,monthly,quarterly',
            'loanEndDate' => 'required|date|after:today',
            'paymentMethod' => 'required|in:cash,mobile_money',
            'paymentAmount' => 'required|numeric|min:1000',
            'phoneNumber' => ['string', 'regex:/^(\+255|0)[0-9]{9}$/'],
            'provider' => 'in:Mpesa,TigoPesa,AirtelMoney,HaloPesa',
            'paymentMethod' => 'required|in:mobile_money,cash',
            'loan_start_date' => 'required|date',
            'receipt' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:10240',
            'rejection_reason' => 'required_if:showRejectModal,true|string|min:5|max:1000',
        ];

        foreach ($this->documents as $key => $value) {
            if (!in_array($key, $this->uploadedDocumentTypes)) {
                $rules["documents.{$key}"] = 'nullable|file|mimes:pdf,png,jpg,jpeg,docx|max:10240';
            }
        }

        return $rules;
    }

    public function mount(Loan $loan)
    {
        $this->loan = $loan->load(['user', 'privateGuarantor', 'governmentGuarantor', 'installation']);
        $this->loanId = $loan->id;
        $this->paymentStatus = 'Not started';
        $this->loanInstallationId = $loan->installation?->id;
        $this->cylinderType = optional($loan->installation)->cylinder_type_id;
        $this->loanRequiredAmount = $loan->loan_required_amount;
        $this->loanPaymentPlan = $loan->loan_payment_plan ?? 'weekly';
        $this->loanEndDate = $loan->loan_end_date ? \Carbon\Carbon::parse($loan->loan_end_date)->format('Y-m-d') : null;
        $this->phoneNumber = $loan->user?->phone_number ?? null;
        $this->loan_start_date = $loan->loan_start_date ? \Carbon\Carbon::parse($loan->loan_start_date)->format('Y-m-d') : now()->format('Y-m-d');
        $this->provider = 'Mpesa';
        $this->paymentMethod = 'cash';
        $this->cylinders = CylinderType::all();
        $this->uploadedDocumentTypes = LoanDocument::where('loan_id', $this->loanId)->pluck('document_type')->toArray();

        Log::info('LoanApproval component mounted', [
            'loan_id' => $this->loanId,
            'user_id' => $this->loan->user?->id,
            'payment_method' => $this->paymentMethod,
            'uploaded_documents' => $this->uploadedDocumentTypes,
            'loan_start_date' => $this->loan_start_date,
        ]);
    }

    public function uploadDocuments()
    {
        $this->validateOnly('documents.*');

        Log::info('Document upload started', ['loan_id' => $this->loanId]);

        try {
            DB::beginTransaction();
            $userName = str_replace(' ', '_', strtolower($this->loan->user?->name ?? 'user_' . $this->loanId));
            $timestamp = now()->format('YmdHis');

            Storage::disk('public')->makeDirectory('client-documents');
            foreach ($this->documents as $key => $file) {
                if ($file && !in_array($key, $this->uploadedDocumentTypes)) {
                    $extension = $file->getClientOriginalExtension();
                    $fileName = "{$userName}_{$key}_{$timestamp}.{$extension}";
                    $path = $file->storeAs('client-documents', $fileName, 'public');

                    $fileExists = Storage::disk('public')->exists($path);
                    if (!$fileExists) {
                        throw new \Exception("Failed to store file: $fileName");
                    }

                    LoanDocument::create([
                        'loan_id' => $this->loanId,
                        'document_type' => $key,
                        'document_path' => $path,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $this->uploadedDocumentTypes[] = $key;
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
            Log::info('Document upload completed', [
                'loan_id' => $this->loanId,
                'uploaded_documents' => $this->uploadedDocumentTypes,
            ]);
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

        Log::info('Loan Approval started', [
            'loan_id' => $this->loanId,
            'payment_method' => $this->paymentMethod,
        ]);

        try {
            DB::beginTransaction();

        
            $requiredDocs = [
                'mktaba_wa_mkopo',
                'kitambulisho_mwomba_mbele',
                'kitambulisho_mdhamini_1_mbele',
                'kitambulisho_mdhamini_2_mbele',
                'leseni_mwomba',
                'kadi_ya_usafiri',
                'barua_ya_utambulisho',
            ];
            $missingDocs = array_diff($requiredDocs, $this->uploadedDocumentTypes);
            if (!empty($missingDocs)) {
                throw new \Exception('Missing required documents: ' . implode(', ', $missingDocs));
            }

          
            $user = $this->loan->user;
            if (!$user) {
                throw new \Exception('No valid user associated with loan ID: ' . $this->loanId);
            }

            $this->loan->update([
                'loan_required_amount' => $this->loanRequiredAmount,
                'loan_payment_plan' => $this->loanPaymentPlan,
                'loan_end_date' => $this->loanEndDate,
                'loan_start_date' => $this->loan_start_date,
                'status' => 'approved',
            ]);

            $installation = Installation::findOrFail($this->loanInstallationId);
            $installation->update(['cylinder_type_id' => $this->cylinderType]);

         
            if ($this->paymentMethod === 'mobile_money') {
                $normalizedPhone = $this->normalizePhoneNumber($this->phoneNumber);
                InitiatePaymentJob::dispatch($this->loanId, $this->paymentAmount, $normalizedPhone, $this->provider);
                Log::info('Mobile money payment dispatched', [
                    'loan_id' => $this->loanId,
                    'amount' => $this->paymentAmount,
                    'phone_number' => $normalizedPhone,
                    'provider' => $this->provider,
                ]);
            } elseif ($this->paymentMethod === 'cash') {
                
                $transactionId = 'CASH_' . $this->loanId . '_' . Str::uuid();
                Payment::create([
                    'loan_id' => $this->loanId,
                    'user_id' => $user->id,
                    'paid_amount' => $this->paymentAmount,
                    'payment_date' => now()->format('Y-m-d'),
                    'transaction_id' => $transactionId,
                    'external_id' => $transactionId,
                    'status' => 'pending',
                    'job_status' => 'completed',
                    'payment_method' => 'cash',
                    'provider' => 'Cash',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                Log::info('Cash payment recorded (no receipt)', [
                    'loan_id' => $this->loanId,
                    'transaction_id' => $transactionId,
                ]);
            }

            DB::commit();


            session()->flash('message', $this->paymentMethod === 'cash'
                ? 'Loan approved and cash payment recorded, pending verification.'
                : 'Loan approval initiated. You will be notified once the payment is processed.');
            return redirect()->to('/');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Loan approval error: ' . $e->getMessage(), [
                'loan_id' => $this->loanId,
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('error', 'Failed to approve loan: ' . $e->getMessage());
        }
    }

    public function checkPaymentStatus()
    {
        $payment = Payment::where('loan_id', $this->loanId)->latest()->first();
        if ($payment) {
            $this->paymentStatus = $payment->job_status;
            if ($payment->job_status === 'completed') {
                session()->flash('message', 'Payment ' . ($payment->payment_method === 'cash' ? 'recorded, pending verification.' : 'initiated successfully! Please complete the transaction on your phone.'));
            } elseif ($payment->job_status === 'failed') {
                session()->flash('error', 'Payment failed. Please try again or contact support.');
            }
        } else {
            $this->paymentStatus = 'Not started';
        }
        Log::info('Payment status checked', [
            'loan_id' => $this->loanId,
            'status' => $this->paymentStatus,
        ]);
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
        try {
            DB::beginTransaction();
            $this->loan->update([
                'status' => 'rejected',
                'rejection_reason' => $this->rejection_reason,
            ]);
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
            'uploadedDocumentTypes' => $this->uploadedDocumentTypes,
        ]);
    }
}

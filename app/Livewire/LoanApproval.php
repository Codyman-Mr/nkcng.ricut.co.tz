<?php
// namespace App\Livewire;

// use App\Jobs\InitiatePaymentJob;
// use App\Jobs\SendSmsJob;
// use App\Models\Installation;
// use App\Models\Loan;
// use App\Models\Payment;
// use App\Models\LoanDocument;
// use App\Models\User;
// use App\Events\PaymentStatusUpdated;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Storage;
// use Livewire\Component;
// use Livewire\WithFileUploads;
// use App\Models\CylinderType;

// class LoanApproval extends Component
// {
//     use WithFileUploads;

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
//     public $paymentMethod;

//     public $documents = [
//         'mktaba_wa_mkopo' => null,
//         'kitambulisho_mwomba_mbele' => null,
//         'kitambulisho_mdhamini_1_mbele' => null,
//         'kitambulisho_mdhamini_2_mbele' => null,
//         'leseni_mwomba' => null,
//         'kadi_ya_usafiri' => null,
//         'barua_ya_utambulisho' => null,
//     ];
//     public $uploadedDocumentTypes = [];

//     public function rules()
//     {
//         $rules = [
//             'cylinderType' => 'required|integer|exists:cylinder_types,id',
//             'loanRequiredAmount' => 'required|numeric|min:1000',
//             'loanPaymentPlan' => 'required|in:weekly,monthly,quarterly',
//             'loanEndDate' => 'required|date|after:today',
//             'paymentAmount' => 'required|numeric|min:1000',
//             'phoneNumber' => ['string', 'regex:/^(\+255|0)[0-9]{9}$/'],
//             'provider' => 'in:Mpesa,TigoPesa,AirtelMoney,HaloPesa',
//             'paymentMethod' => 'required|in:mobile_money,cash',
//             'rejection_reason' => 'required_if:showRejectModal,true|string|min:5|max:1000',
//         ];

//         // Only validate document fields that haven't been uploaded yet
//         foreach ($this->documents as $key => $value) {
//             if (!in_array($key, $this->uploadedDocumentTypes)) {
//                 $rules["documents.{$key}"] = 'nullable|file|mimes:pdf,png,jpg,jpeg,docx|max:10240';
//             }
//         }

//         return $rules;
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
//         $this->uploadedDocumentTypes = LoanDocument::where('loan_id', $this->loanId)->pluck('document_type')->toArray();
//         Log::info('LoanApproval component mounted', [
//             'loan_id' => $this->loanId,
//             'user_id' => $this->loan->user ? $this->loan->user->id : null,
//             'has_user' => !is_null($this->loan->user),
//             'uploaded_documents' => $this->uploadedDocumentTypes,
//         ]);
//     }

//     public function uploadDocuments()
//     {
//         $this->validateOnly('documents.*');
//         Log::info('Document upload started', ['loan_id' => $this->loanId]);

//         try {
//             DB::beginTransaction();
//             $userName = str_replace(' ', '_', strtolower($this->loan->user->name ?? 'user_' . $this->loanId));
//             $timestamp = now()->format('YmdHis');

//             Storage::disk('public')->makeDirectory('client-documents');
//             $diskPath = Storage::disk('public')->path('');
//             Log::info('Storage disk info', [
//                 'loan_id' => $this->loanId,
//                 'disk_path' => $diskPath,
//                 'directory_exists' => Storage::disk('public')->exists('client-documents'),
//             ]);

//             foreach ($this->documents as $key => $file) {
//                 if ($file && !in_array($key, $this->uploadedDocumentTypes)) {
//                     $extension = $file->getClientOriginalExtension();
//                     $fileName = "{$userName}_{$key}_{$timestamp}.{$extension}";
//                     $path = $file->storeAs('client-documents', $fileName, 'public');

//                     $fileExists = Storage::disk('public')->exists($path);
//                     $absolutePath = storage_path('app/public/' . $path);
//                     Log::info('Document storage attempt', [
//                         'loan_id' => $this->loanId,
//                         'document_type' => $key,
//                         'file_name' => $fileName,
//                         'path' => $path,
//                         'absolute_path' => $absolutePath,
//                         'file_exists' => $fileExists,
//                     ]);

//                     if (!$fileExists) {
//                         throw new \Exception("Failed to store file: {$fileName}");
//                     }

//                     LoanDocument::create([
//                         'loan_id' => $this->loanId,
//                         'document_type' => $key,
//                         'document_path' => $path,
//                         'created_at' => now(),
//                         'updated_at' => now(),
//                     ]);

//                     $this->uploadedDocumentTypes[] = $key;
//                     Log::info('Document uploaded', [
//                         'loan_id' => $this->loanId,
//                         'document_type' => $key,
//                         'path' => $path,
//                     ]);
//                 }
//             }

//             DB::commit();
//             session()->flash('message', 'Documents uploaded successfully.');
//             $this->reset('documents');
//             Log::info('Document upload completed', [
//                 'loan_id' => $this->loanId,
//                 'uploaded_documents' => $this->uploadedDocumentTypes,
//             ]);
//         } catch (\Exception $e) {
//             DB::rollback();
//             Log::error('Document upload error: ' . $e->getMessage(), [
//                 'loan_id' => $this->loanId,
//                 'trace' => $e->getTraceAsString(),
//             ]);
//             session()->flash('error', 'Failed to upload documents: ' . $e->getMessage());
//         }
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

//             // Check if all required documents are uploaded
//             $requiredDocs = [
//                 'mktaba_wa_mkopo',
//                 'kitambulisho_mwomba_mbele',
//                 'kitambulisho_mdhamini_1_mbele',
//                 'kitambulisho_mdhamini_2_mbele',
//                 'leseni_mwomba',
//                 'kadi_ya_usafiri',
//                 'barua_ya_utambulisho',
//             ];
//             $missingDocs = array_diff($requiredDocs, $this->uploadedDocumentTypes);
//             if (!empty($missingDocs)) {
//                 throw new \Exception('Missing required documents: ' . implode(', ', $missingDocs));
//             }
//             Log::info('Document validation passed', ['loan_id' => $this->loanId]);

//             // Ensure loan has a valid user
//             if (!$this->loan->user || !$this->loan->user->id) {
//                 throw new \Exception('No user associated with loan ID: ' . $this->loanId);
//             }
//             $user = User::find($this->loan->user->id);
//             if (!$user) {
//                 throw new \Exception('User ID ' . $this->loan->user->id . ' does not exist in users table for loan ID: ' . $this->loanId);
//             }
//             Log::info('User validation passed', [
//                 'loan_id' => $this->loanId,
//                 'user_id' => $this->loan->user->id,
//                 'user_exists' => !is_null($user),
//             ]);

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

//             $normalizedPhone = $this->normalizePhoneNumber($this->phoneNumber);

//             Log::info('Initiating payment job', [
//                 'loan_id' => $this->loanId,
//                 'user_id' => $this->loan->user->id,
//                 'amount' => $this->paymentAmount,
//                 'phone_number' => $normalizedPhone,
//                 'provider' => $this->provider,
//             ]);
//             InitiatePaymentJob::dispatch($this->loanId, $this->paymentAmount, $normalizedPhone, $this->provider);

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

//             $recipients = array_unique($recipients);

//             if (!empty($recipients)) {
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
//             'uploadedDocumentTypes' => $this->uploadedDocumentTypes,
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
        // $rules = [
        //     'cylinderType' => 'required|integer|exists:cylinder_types,id',
        //     'loanRequiredAmount' => 'required|numeric|min:1000',
        //     'loanPaymentPlan' => 'required|in:weekly,monthly,quarterly',
        //     'loanEndDate' => 'required|date|after:today',
        //     'paymentAmount' => 'required|numeric|min:1000',
        //     'paymentMethod' => 'required|in:cash,mobile_money',
        //     'phoneNumber' => 'required_if:paymentMethod,mobile_money|regex:/^(\+255|0)[0-9]{9}$/',
        //     'provider' => 'required_if:paymentMethod,mobile_money|in:Mpesa,TigoPesa,AirtelMoney,HaloPesa',
        //     'receipt' => 'required_if:paymentMethod,cash|file|mimes:pdf,png,jpg,jpeg|max:10240',
        //     'rejection_reason' => 'required_if:showRejectModal,true|string|min:5|max:1000',
        // ];

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
        $this->paymentAmount = $loan->loan_required_amount ? $this->loanRequiredAmount / 10 : null;
        $this->provider = 'Mpesa';
        $this->paymentMethod = 'cash';
        $this->cylinders = CylinderType::all();
        $this->uploadedDocumentTypes = LoanDocument::where('loan_id', $this->loanId)->pluck('document_type')->toArray();
        Log::info('LoanApproval component mounted', [
            'loan_id' => $this->loanId,
            'user_id' => $this->loan->user?->id,
            'payment_method' => $this->paymentMethod,
            'uploaded_documents' => $this->uploadedDocumentTypes,
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

            // Check required documents
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

            // Ensure loan has a valid user
            $user = $this->loan->user;
            if (!$user) {
                throw new \Exception('No valid user associated with loan ID: ' . $this->loanId);
            }

            $this->loan->update([
                'loan_required_amount' => $this->loanRequiredAmount,
                'loan_payment_plan' => $this->loanPaymentPlan,
                'loan_end_date' => $this->loanEndDate,
                'status' => 'approved',
            ]);

            $installation = Installation::findOrFail($this->loanInstallationId);
            $installation->update(['cylinder_type_id' => $this->cylinderType]);

            // Handle payment based on payment method
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
                // Validate and upload receipt
                $this->validateOnly('receipt');
                $userName = str_replace(' ', '_', strtolower($this->loan->user?->name ?? 'user_' . $this->loanId));
                $timestamp = now()->format('YmdHis');
                Storage::disk('public')->makeDirectory('cash-receipts');
                $extension = $this->receipt->getClientOriginalExtension();
                $fileName = "{$userName}_receipt_{$timestamp}.{$extension}";
                $path = $this->receipt->storeAs('cash-receipts', $fileName, 'public');

                $fileExists = Storage::disk('public')->exists($path);
                if (!$fileExists) {
                    throw new \Exception("Failed to store receipt: $fileName");
                }

                // Create payment record
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
                    'receipt_path' => $path,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                Log::info('Cash payment recorded', [
                    'loan_id' => $this->loanId,
                    'transaction_id' => $transactionId,
                    'receipt_path' => $path,
                ]);
            }

            DB::commit();

            $recipients = array_unique(array_filter([
                $user->phone_number ? $this->normalizePhoneNumber($user->phone_number) : null,
                $this->loan->privateGuarantor?->phone_number ? $this->normalizePhoneNumber($this->loan->privateGuarantor->phone_number) : null,
                $this->loan->governmentGuarantor?->phone_number ? $this->normalizePhoneNumber($this->loan->governmentGuarantor->phone_number) : null,
            ]));

            if (!empty($recipients)) {
                $message = $this->paymentMethod === 'cash'
                    ? "Loan #{$this->loanId} approved! Cash payment of TZS {$this->paymentAmount} recorded, pending verification."
                    : "Loan #{$this->loanId} approved! Payment of TZS {$this->paymentAmount} is being processed.";
                SendSmsJob::dispatch($recipients, $message, $this->loanId);
                Log::info('SMS job dispatched', ['loan_id' => $this->loanId, 'recipients' => $recipients]);
            }

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

<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\CustomerVehicle;
use App\Models\Installation;
use App\Models\Loan;
use App\Models\LoanPackage;
use App\Models\GovernmentGuarantor;
use App\Models\PrivateGuarantor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;  // added for HTTP requests

class LoanApplicationForm extends Component
{
    use WithFileUploads;

    public $dob;
    public $gender = 'male';

    public $first_name;
    public $last_name;
    public $phone_number;
    public $nida_no;
    public $address;
    public $vehicle_name;
    public $vehicle_type;
    public $plate_number;
    public $fuel_type;
    public $gvt_guarantor_first_name;
    public $gvt_guarantor_last_name;
    public $gvt_guarantor_phone_number;
    public $gvt_guarantor_nida_no;
    public $private_guarantor_first_name;
    public $private_guarantor_last_name;
    public $private_guarantor_phone_number;
    public $private_guarantor_nida_no;
    public $loan_package_id;
    public $hasExistingLoan = false;

    public $progress = 0;
    public $currentStep = 1;
    public $totalSteps = 3;

    public function rules()
    {
        return [
            'dob' => ['required', 'date', 'before:' . now()->subYears(18)->format('Y-m-d')],
            'gender' => 'required|in:male,female',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => [
                'required',
                'string',
                'max:13',
                'regex:/^(0|\+255)(6|7|8)\d{8}$/',
                'unique:users,phone_number',
            ],
            'nida_no' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'vehicle_name' => 'required|string|max:255',
            'vehicle_type' => 'required|string|in:car,bajaj',
            'plate_number' => 'required|string|max:255',
            'fuel_type' => 'required|string|in:petrol,diesel',
            'gvt_guarantor_first_name' => 'required|string|max:255',
            'gvt_guarantor_last_name' => 'required|string|max:255',
            'gvt_guarantor_phone_number' => [
                'required',
                'string',
                'max:13',
                'regex:/^(0|\+255)(6|7|8)\d{8}$/',
                'unique:government_guarantors,phone_number',
                'different:private_guarantor_phone_number',
            ],
            'gvt_guarantor_nida_no' => [
                'required',
                'string',
                'max:20',
                'unique:government_guarantors,nida_no',
                'different:private_guarantor_nida_no',
            ],
            'private_guarantor_first_name' => 'required|string|max:255',
            'private_guarantor_last_name' => 'required|string|max:255',
            'private_guarantor_phone_number' => [
                'required',
                'string',
                'max:13',
                'regex:/^(0|\+255)(6|7|8)\d{8}$/',
                'unique:private_guarantors,phone_number',
                'different:gvt_guarantor_phone_number',
            ],
            'private_guarantor_nida_no' => [
                'required',
                'string',
                'max:20',
                'unique:private_guarantors,nida_no',
                'different:gvt_guarantor_nida_no',
            ],
            'loan_package_id' => 'required|exists:loan_packages,id',
        ];
    }

    public function messages()
    {
        return [
            'gvt_guarantor_phone_number.regex' => 'The government guarantor phone number must be a valid Tanzanian number (e.g., +255712345678 or 0712345678).',
            'private_guarantor_phone_number.regex' => 'The private guarantor phone number must be a valid Tanzanian number (e.g., +255712345678 or 0712345678).',
            'gvt_guarantor_phone_number.unique' => 'This government guarantor phone number is already registered.',
            'private_guarantor_phone_number.unique' => 'This private guarantor phone number is already registered.',
            'gvt_guarantor_nida_no.unique' => 'This government guarantor NIDA number is already registered.',
            'private_guarantor_nida_no.unique' => 'This private guarantor NIDA number is already registered.',
            'gvt_guarantor_phone_number.different' => 'The government and private guarantor phone numbers must be different.',
            'private_guarantor_phone_number.different' => 'The private and government guarantor phone numbers must be different.',
            'gvt_guarantor_nida_no.different' => 'The government and private guarantor NIDA numbers must be different.',
            'private_guarantor_nida_no.different' => 'The private and government guarantor NIDA numbers must be different.',
        ];
    }

    public function mount($loan_package_id)
    {
        $this->currentStep = 1;
        $this->loan_package_id = $loan_package_id;

        $loanPackage = LoanPackage::with('cylinder')->find($loan_package_id);
        Log::info('Mount loan package', ['loan_package_id' => $loan_package_id]);

        if (!$loanPackage || !$loanPackage->cylinder) {
            Log::error('Invalid or missing loan package/cylinder', ['loan_package_id' => $loan_package_id]);
            session()->flash('error', 'Invalid loan package configuration.');
            return redirect()->to('/');
        }

        $user = Auth::user();
        $existingLoan = Loan::where('user_id', $user->id)
            ->where('status', 'approved')
            ->exists();
       
    }

    private function getStepRules()
    {
        $allRules = $this->rules();
        Log::info('Validation rules for step', ['step' => $this->currentStep, 'rules' => $allRules]);
        return match ($this->currentStep) {
            1 => array_intersect_key($allRules, array_flip([
                'dob',
                'gender',
                'first_name',
                'last_name',
                'phone_number',
                'nida_no',
                'address',
                'loan_package_id',
            ])),
            2 => array_intersect_key($allRules, array_flip([
                'vehicle_name',
                'vehicle_type',
                'plate_number',
                'fuel_type',
            ])),
            3 => array_intersect_key($allRules, array_flip([
                'gvt_guarantor_first_name',
                'gvt_guarantor_last_name',
                'gvt_guarantor_phone_number',
                'gvt_guarantor_nida_no',
                'private_guarantor_first_name',
                'private_guarantor_last_name',
                'private_guarantor_phone_number',
                'private_guarantor_nida_no',
            ])),
            default => [],
        };
    }

    private function normalizePhoneNumber($phoneNumber)
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

    public function submit()
    {
        if (config('app.debug')) {
            DB::enableQueryLog();
        }

        Log::info('Submit method triggered', ['loan_package_id' => $this->loan_package_id]);

        $loanPackage = LoanPackage::with('cylinder')->find($this->loan_package_id);
        if (!$loanPackage || !$loanPackage->cylinder) {
            Log::error('Invalid or missing loan package/cylinder', ['loan_package_id' => $this->loan_package_id]);
            session()->flash('error', 'Selected loan package is invalid.');
            return;
        }

        try {
            Log::info('Starting validation', ['rules' => $this->rules()]);
            $this->validate();
            Log::info('Validation passed');

            DB::beginTransaction();
            Log::info('Database transaction started');

            $user = Auth::user();
            Log::info('User retrieved: ' . $user->id);

            $existingLoan = Loan::where('user_id', $user->id)
                ->where('status', 'approved')
                ->exists();

            if ($existingLoan && $user->role !== 'admin') {
                Log::info('Existing loan found - aborting');
                $this->hasExistingLoan = true;
                session()->flash('error', 'You already have an approved loan. Please complete or cancel your existing loan.');
                return;
            }

            Log::info('Creating vehicle');
            $vehicle = CustomerVehicle::create([
                'user_id' => $user->id,
                'model' => $this->vehicle_name,
                'plate_number' => $this->plate_number,
                'vehicle_type' => $this->vehicle_type,
                'fuel_type' => $this->fuel_type,
            ]);
            Log::info('Vehicle created: ' . $vehicle->id);

            Log::info('Creating installation');
            $installation = Installation::create([
                'customer_vehicle_id' => $vehicle->id,
                'cylinder_type_id' => $loanPackage->cylinder->id,
                'status' => 'pending',
                'payment_type' => 'loan',
            ]);

            Log::info('Installation created: ' . $installation->id);

            Log::info('Creating loan');
            $loan = Loan::create([
                'user_id' => $user->id,
                'installation_id' => $installation->id,
                'loan_required_amount' => (float) $loanPackage->amount_to_finance,
                'status' => 'pending',
                'applicant_name' => $this->first_name . ' ' . $this->last_name,
                'applicant_phone_number' => $this->phone_number,
            ]);

            Log::info('Loan created: ' . $loan->id);

        
            $existingGovernmentGuarantor = GovernmentGuarantor::where('loan_id', $loan->id)->first();
            $existingPrivateGuarantor = PrivateGuarantor::where('loan_id', $loan->id)->first();
            if ($existingGovernmentGuarantor || $existingPrivateGuarantor) {
                throw new \Exception('Guarantor records already exist for this loan.');
            }

        
            $gvtPhoneNumber = $this->normalizePhoneNumber($this->gvt_guarantor_phone_number);
            $privatePhoneNumber = $this->normalizePhoneNumber($this->private_guarantor_phone_number);

            Log::info('Creating government guarantor', [
                'loan_id' => $loan->id,
                'first_name' => $this->gvt_guarantor_first_name,
                'last_name' => $this->gvt_guarantor_last_name,
                'phone_number' => $gvtPhoneNumber,
                'nida_no' => $this->gvt_guarantor_nida_no,
            ]);
            try {
                $governmentGuarantor = GovernmentGuarantor::create([
                    'loan_id' => $loan->id,
                    'first_name' => $this->gvt_guarantor_first_name,
                    'last_name' => $this->gvt_guarantor_last_name,
                    'phone_number' => $gvtPhoneNumber,
                    'nida_no' => $this->gvt_guarantor_nida_no,
                ]);
            } catch (QueryException $e) {
                throw new \Exception('Failed to create government guarantor: ' . $e->getMessage());
            }

            Log::info('Creating private guarantor', [
                'loan_id' => $loan->id,
                'first_name' => $this->private_guarantor_first_name,
                'last_name' => $this->private_guarantor_last_name,
                'phone_number' => $privatePhoneNumber,
                'nida_no' => $this->private_guarantor_nida_no,
            ]);
            try {
                $privateGuarantor = PrivateGuarantor::create([
                    'loan_id' => $loan->id,
                    'first_name' => $this->private_guarantor_first_name,
                    'last_name' => $this->private_guarantor_last_name,
                    'phone_number' => $privatePhoneNumber,
                    'nida_no' => $this->private_guarantor_nida_no,
                ]);
            } catch (QueryException $e) {
                throw new \Exception('Failed to create private guarantor: ' . $e->getMessage());
            }

            DB::commit();
            Log::info('Database transaction committed');

           
            $this->sendNotifications(
                $user,
                $loan,
                $governmentGuarantor,
                $privateGuarantor,
                $this->first_name,
                $this->last_name,
                $this->phone_number
            );
            Log::info('Notifications sent', [
                'loan_id' => $loan->id,
                'user_id' => $user->id,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'phone_number' => $this->phone_number,
                'government_guarantor_id' => $governmentGuarantor->id,
                'private_guarantor_id' => $privateGuarantor->id,
            ]);

            session()->flash('message', 'Loan application submitted successfully!');
            Log::info('Redirecting to home');
            return redirect()->to('/');

        } catch (\Exception $e) {
            Log::error('Error occurred: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            DB::rollback();
            session()->flash('error', 'An error occurred while submitting the application: ' . $e->getMessage());
        }

        if (config('app.debug')) {
            Log::debug('Query log', DB::getQueryLog());
        }
    }
protected function sendNotifications(
    User $user,
    Loan $loan,
    GovernmentGuarantor $governmentGuarantor,
    PrivateGuarantor $privateGuarantor,
    string $formFirstName,
    string $formLastName,
    string $formPhoneNumber
) {
    $username = 'MIKE001';
    $apiKey = 'atsk_a37133bcba27a4928705557b9903b016812000533f89a91f06747a289a8654dca1dac55d';
    $from = 'NK CNG';
    $enqueue = 1;

    $normalize = function ($number) {
        $number = preg_replace('/\D/', '', $number);
        if (preg_match('/^0(6|7|8)\d{8}$/', $number)) {
            return '+255' . substr($number, 1);
        } elseif (preg_match('/^255(6|7|8)\d{8}$/', $number)) {
            return '+' . $number;
        }
        return $number;
    };

    $recipients = [
        [
            'to' => $normalize($formPhoneNumber),
            'message' => "Hellow {$formFirstName},Your loan application has been submitted! It is being processed, and you will receive more information within 7 days.",
        ],
        [
            'to' => $normalize($governmentGuarantor->phone_number),
            'message' => "Hellow {$governmentGuarantor->first_name} {$governmentGuarantor->last_name},You have been selected by {$formFirstName} {$formLastName} to be a guarantor for their loan from NKCNG. We will contact you within 7 days for further details.",
        ],
        [
            'to' => $normalize($privateGuarantor->phone_number),
            'message' => "Hellow {$privateGuarantor->first_name} {$privateGuarantor->last_name}, You have been selected by{$formFirstName} {$formLastName} to be a guarantor for their loan from NKCNG. We will contact you within 7 days for further details.",
        ],
    ];

    foreach ($recipients as $recipient) {
        $phone = $recipient['to'];
        $message = $recipient['message'];

        if (!$phone || strlen($phone) < 10) {
            Log::warning("SMS not sent. Invalid phone number.", ['phone' => $phone]);
            continue;
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'apiKey' => $apiKey,
            ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
                'username' => $username,
                'to' => $phone,
                'message' => $message,
                'from' => $from,
                'enqueue' => $enqueue,
            ]);

            if ($response->successful()) {
                Log::info("SMS sent successfully", [
                    'to' => $phone,
                    'message' => $message,
                    'response' => $response->json(),
                ]);
            } else {
                Log::error("Failed to send SMS", [
                    'to' => $phone,
                    'message' => $message,
                    'response' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error("SMS exception occurred", [
                'to' => $phone,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

    public function next()
    {
        try {
            Log::info('Navigating to step ' . ($this->currentStep + 1), ['rules' => $this->getStepRules()]);
            $this->validate($this->getStepRules());
            if ($this->currentStep < $this->totalSteps) {
                $this->currentStep++;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed on step ' . $this->currentStep, ['errors' => $e->errors()]);
            return;
        } catch (\Throwable $e) {
            Log::error('Error occurred during step navigation: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function previous()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function render()
    {
        $borrower = Auth::user();
        return view('livewire.loan-application-form', [
            'borrower' => $borrower,
            'currentStep' => $this->currentStep,
            'totalSteps' => $this->totalSteps,
            'loanPackages' => LoanPackage::with('cylinder')->get(),
        ]);
    }
}

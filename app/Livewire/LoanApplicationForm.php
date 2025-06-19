<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Jobs\SendSmsJob;
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

class LoanApplicationForm extends Component
{
    use WithFileUploads;

    public $dob;
    public $gender = 'male';
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
                'max:15',
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
                'max:15',
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
        // if ($existingLoan) {
        //     Log::info('Existing loan found during mount');
        //     $this->hasExistingLoan = true;
        //     session()->flash('error', 'You already have an approved loan. Please <a href="/loans" class="underline">view your existing loan</a> or contact support for assistance.');
        //     return redirect()->to('/');
        // }
    }

    private function getStepRules()
    {
        $allRules = $this->rules();
        Log::info('Validation rules for step', ['step' => $this->currentStep, 'rules' => $allRules]);
        return match ($this->currentStep) {
            1 => array_intersect_key($allRules, array_flip([
                'dob',
                'gender',
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
            if ($existingLoan) {
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
            ]);
            Log::info('Loan created: ' . $loan->id);

            // Check for existing guarantor records
            $existingGovernmentGuarantor = GovernmentGuarantor::where('loan_id', $loan->id)->first();
            $existingPrivateGuarantor = PrivateGuarantor::where('loan_id', $loan->id)->first();
            if ($existingGovernmentGuarantor || $existingPrivateGuarantor) {
                throw new \Exception('Guarantor records already exist for this loan.');
            }

            // Normalize phone numbers
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
                // Log::info('Government guarantor created: ' . ($governmentGuarantor->id ?? 'failed'), ['guarantor' => $governmentGuarantor->toArray()]);

                // if (!$governmentGuarantor->id) {
                //     throw new \Exception('Failed to create government guarantor record.');
                // }
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
                // Log::info('Private guarantor created: ' . ($privateGuarantor->id ?? 'failed'), ['guarantor' => $privateGuarantor->toArray()]);

                // if (!$privateGuarantor->id) {
                //     throw new \Exception('Failed to create private guarantor record.');
                // }
            } catch (QueryException $e) {
                throw new \Exception('Failed to create private guarantor: ' . $e->getMessage());
            }

            DB::commit();
            Log::info('Database transaction committed');

            // Send SMS notifications
            $this->sendNotifications($user, $loan, $governmentGuarantor, $privateGuarantor);

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

    /**
     * Send SMS notifications to the user and guarantors using SendSmsJob.
     *
     * @param User $user
     * @param Loan $loan
     * @param GovernmentGuarantor $governmentGuarantor
     * @param PrivateGuarantor $privateGuarantor
     */
    protected function sendNotifications(User $user, Loan $loan, GovernmentGuarantor $governmentGuarantor, PrivateGuarantor $privateGuarantor)
    {
        $recipients = [];
        $messages = [];

        // User notification
        if ($user->phone_number) {
            $normalizedUserPhone = $this->normalizePhoneNumber($user->phone_number);
            $recipients[] = $normalizedUserPhone;
            $messages[$normalizedUserPhone] = "Habari {$user->first_name}, maombi yako ya mkopo yamewasilishwa! Yanashughulikiwa, utapata taarifa zaidi siku 7.";
        } else {
            Log::warning('User phone number missing for SMS notification', ['user_id' => $user->id, 'loan_id' => $loan->id]);
            session()->flash('warning', 'Loan submitted, but we couldn’t send an SMS notification to the user. Please check your phone number.');
        }

        // Government guarantor notification
        if ($governmentGuarantor->phone_number) {
            $normalizedGvtPhone = $this->normalizePhoneNumber($governmentGuarantor->phone_number);
            $recipients[] = $normalizedGvtPhone;
            $messages[$normalizedGvtPhone] = "Habari {$governmentGuarantor->first_name} {$governmentGuarantor->last_name}, umechaguliwa na {$user->first_name} {$user->last_name} kuwa mdhamini wa mkopo wake kutoka NKCNG. Tutawasiliana nawe ndani ya siku 7 kwa maelezo zaidi.";
        } else {
            Log::warning('Government guarantor phone number missing for SMS notification', [
                'guarantor_id' => $governmentGuarantor->id,
                'loan_id' => $loan->id,
            ]);
            session()->flash('warning', 'Loan submitted, but we couldn’t send an SMS to the government guarantor.');
        }

        // Private guarantor notification
        if ($privateGuarantor->phone_number) {
            $normalizedPrivatePhone = $this->normalizePhoneNumber($privateGuarantor->phone_number);
            $recipients[] = $normalizedPrivatePhone;
            $messages[$normalizedPrivatePhone] = "Habari {$privateGuarantor->first_name} {$privateGuarantor->last_name}, umechaguliwa na {$user->first_name} {$user->last_name} kuwa mdhamini wa mkopo wake kutoka NKCNG. Tutawasiliana nawe ndani ya siku 7 kwa maelezo zaidi.";
        } else {
            Log::warning('Private guarantor phone number missing for SMS notification', [
                'guarantor_id' => $privateGuarantor->id,
                'loan_id' => $loan->id,
            ]);
            session()->flash('warning', 'Loan submitted, but we couldn’t send an SMS to the private guarantor.');
        }

        // Remove duplicate recipients
        $recipients = array_unique($recipients);

        if (empty($recipients)) {
            Log::warning('No valid recipients for SMS notifications', ['loan_id' => $loan->id]);
            session()->flash('warning', 'Loan submitted, but no SMS notifications were sent due to missing phone numbers.');
            return;
        }

        try {
            // Dispatch individual SendSmsJob for each recipient with their specific message
            $successCount = 0;
            foreach ($recipients as $recipient) {
                if (isset($messages[$recipient])) {
                    SendSmsJob::dispatch([$recipient], $messages[$recipient], $loan->id);
                    $successCount++;
                    Log::info('SMS job dispatched for recipient', [
                        'recipient' => $recipient,
                        'loan_id' => $loan->id,
                        'message' => $messages[$recipient],
                    ]);
                }
            }

            if ($successCount > 0) {
                Log::info('SMS jobs dispatched successfully', ['successful' => $successCount, 'total' => count($recipients), 'loan_id' => $loan->id]);
            } else {
                Log::warning('No SMS jobs dispatched due to missing messages', ['loan_id' => $loan->id]);
                session()->flash('warning', 'Loan submitted, but no SMS notifications were sent.');
            }
        } catch (\Exception $e) {
            Log::error('Error dispatching SMS jobs: ' . $e->getMessage(), [
                'loan_id' => $loan->id,
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('warning', 'Loan submitted, but we couldn’t queue some SMS notifications.');
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

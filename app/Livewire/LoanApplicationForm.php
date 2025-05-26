<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\CustomerVehicle;
use App\Models\Installation;
use App\Models\Loan;
use App\Models\LoanDocument;
use App\Models\LoanPackage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    public $gprivate_guarantor_phone_number;
    public $private_guarantor_nida_no;
    public $loan_package_id; // Changed from $package to $loan_package_id
    public $hasExistingLoan = false;

    public $progress = 0;
    public $currentStep = 1;
    public $totalSteps = 3;

    public function rules()
    {
        return [
            'dob' => ['required', 'date', 'before:' . now()->subYears(18)->format('Y-m-d')],
            'gender' => 'required|in:male,female',
            'nida_no' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'vehicle_name' => 'required|string|max:255',
            'vehicle_type' => 'required|string|in:car,bajaj',
            'plate_number' => 'required|string|max:255',
            'fuel_type' => 'required|string|in:petrol,diesel',
            'gvt_guarantor_first_name' => 'required|string|max:255',
            'gvt_guarantor_last_name' => 'required|string|max:255',
            'gvt_guarantor_phone_number' => 'required|string|max:255',
            'gvt_guarantor_nida_no' => 'required|string|max:255',
            'private_guarantor_first_name' => 'required|string|max:255',
            'private_guarantor_last_name' => 'required|string|max:255',
            'private_guarantor_phone_number' => 'required|string|max:255',
            'private_guarantor_nida_no' => 'required|string|max:255',
            'loan_package_id' => 'required|exists:loan_packages,id',
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
        if ($existingLoan) {
            Log::info('Existing loan found during mount');
            $this->hasExistingLoan = true;
            session()->flash('error', 'You already have an approved loan. Please <a href="/loans" class="underline">view your existing loan</a> or contact support for assistance.');
            return redirect()->to('/');
        }
    }

    private function getStepRules()
    {
        $allRules = $this->rules();
        return match ($this->currentStep) {
            1 => array_intersect_key($allRules, array_flip([
                'dob', 'gender', 'nida_no', 'address', 'loan_package_id',
            ])),
            2 => array_intersect_key($allRules, array_flip([
                'vehicle_name', 'vehicle_type', 'plate_number', 'fuel_type',
            ])),
            3 => array_intersect_key($allRules, array_flip([
                'gvt_guarantor_first_name', 'gvt_guarantor_last_name',
                'gvt_guarantor_phone_number', 'gvt_guarantor_nida_no',
                'private_guarantor_first_name', 'private_guarantor_last_name',
                'private_guarantor_phone_number', 'private_guarantor_nida_no',
            ])),
            default => [],
        };
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
            Log::info('Starting validation');
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
                session()->flash('error', 'You already have an approved loan. Please complete or cancel your existing loan before applying for a new one.');
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

            DB::commit();
            Log::info('Database transaction committed');

            // Send SMS notification
            $this->sendSuccessNotification($user, $loan);

            session()->flash('message', 'Loan application submitted successfully!');
            Log::info('Redirecting to home');
            return redirect()->to('/');

        } catch (\Exception $e) {
            Log::error('Error occurred: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            DB::rollback();
            session()->flash('error', 'An error occurred while submitting the application.');
        }

        if (config('app.debug')) {
            Log::debug('Query log', DB::getQueryLog());
        }
    }

    /**
     * Send SMS notification to the user after successful loan submission.
     *
     * @param User $user
     * @param Loan $loan
     */
    protected function sendSuccessNotification(User $user, Loan $loan)
    {
        try {
            if (empty($user->phone_number)) {
                Log::warning('User phone number missing for SMS notification', ['user_id' => $user->id, 'loan_id' => $loan->id]);
                return;
            }

            $sms = new SendSms();
            $message = "Habari {$user->first_name}, maombi yako ya mkopo yamewasilishwa! Yanashughulikiwa, na utapata taarifa zaidi ndani ya siku 7.";
            $success = $sms->send($user->id, $message);

            if ($success) {
                Log::info('SMS notification sent successfully', ['user_id' => $user->id, 'loan_id' => $loan->id]);
            } else {
                Log::error('Failed to send SMS notification', ['user_id' => $user->id, 'loan_id' => $loan->id]);
                session()->flash('warning', 'Loan submitted, but we couldn’t send an SMS notification. Please check your phone number.');
            }
        } catch (\Exception $e) {
            Log::error('Error sending SMS notification: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'loan_id' => $loan->id,
                'trace' => $e->getTraceAsString(),
            ]);
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
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Error during step navigation: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
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

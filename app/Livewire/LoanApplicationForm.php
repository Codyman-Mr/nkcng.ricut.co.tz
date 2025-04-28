<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\CustomerVehicle;
use App\Models\Installation;
use App\Models\Loan;
use App\Models\LoanDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoanApplicationForm extends Component
{
    use WithFileUploads;

    // Form Fields
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
    public $loan_required_amount;

    // Progress
    public $progress = 0;
    public $currentStep = 1;
    public $totalSteps = 3;
    public $package = [];
    public $hasExistingLoan = false; // New property

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
        ];
    }

    // public function mount($package)
    // {
    //     $this->currentStep = 1;
    //     $this->package = $package;

    //     Log::info('Mount package data', $package);

    //     if (!isset($package['id']) || !isset($package['loan_required_amount'])) {
    //         Log::error('Invalid package structure', $package);
    //         session()->flash('error', 'Invalid loan package configuration.');
    //         return redirect()->to('/');
    //     }
    // }


    public function mount($package)
    {
        $this->currentStep = 1;
        $this->package = $package;

        Log::info('Mount package data', $package);

        if (!isset($package['id']) || !isset($package['loan_required_amount'])) {
            Log::error('Invalid package structure', $package);
            session()->flash('error', 'Invalid loan package configuration.');
            return redirect()->to('/');
        }

        // Check for existing loan
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
                'dob',
                'gender',
                'nida_no',
                'address',
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

    // public function submit()
    // {
    //     if (config('app.debug')) {
    //         DB::enableQueryLog();
    //     }

    //     Log::info('Package data before submission', $this->package);

    //     if (!isset($this->package['id']) || !isset($this->package['loan_required_amount'])) {
    //         $this->addError('package', 'Invalid loan package configuration');
    //         Log::error('Invalid package structure', $this->package);
    //         return;
    //     }

    //     Log::info('Submit method triggered');

    //     try {
    //         Log::info('Starting validation');
    //         $this->validate();
    //         Log::info('Validation passed');

    //         DB::beginTransaction();
    //         Log::info('Database transaction started');

    //         $user = Auth::user();
    //         Log::info('User retrieved: ' . $user->id);

    //         $existingLoan = Loan::where('user_id', $user->id)
    //             ->where('status', 'approved')
    //             ->exists();
    //         Log::info('Existing loan check: ' . ($existingLoan ? 'Exists' : 'None'));

    //         if ($existingLoan) {
    //             Log::info('Existing loan found - aborting');
    //             $this->addError('loan', 'User already has an approved loan.');
    //             return;
    //         }

    //         Log::info('Creating vehicle');
    //         $vehicle = CustomerVehicle::create([
    //             'user_id' => $user->id,
    //             'model' => $this->vehicle_name,
    //             'plate_number' => $this->plate_number,
    //             'vehicle_type' => $this->vehicle_type,
    //             'fuel_type' => $this->fuel_type,
    //         ]);
    //         Log::info('Vehicle created: ' . $vehicle->id);

    //         Log::info('Creating installation');
    //         $installation = Installation::create([
    //             'customer_vehicle_id' => $vehicle->id,
    //             'cylinder_type_id' => $this->package['id'],
    //             'status' => 'pending',
    //             'payment_type' => 'loan',
    //         ]);
    //         Log::info('Installation created: ' . $installation->id);

    //         Log::info('Creating loan');
    //         $loan = Loan::create([
    //             'user_id' => $user->id,
    //             'installation_id' => $installation->id,
    //             'loan_required_amount' => (float) str_replace(',', '', $this->package['loan_required_amount']),
    //         ]);
    //         Log::info('Loan created: ' . $loan->id);

    //         DB::commit();
    //         Log::info('Database transaction committed');

    //         session()->flash('message', 'Loan application submitted successfully!');
    //         Log::info('Redirecting to home');
    //         return redirect()->to('/');

    //     } catch (\Exception $e) {
    //         Log::error('Error occurred: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    //         DB::rollback();
    //         session()->flash('error', 'An error occurred while submitting the application.');
    //     }

    //     if (config('app.debug')) {
    //         Log::debug('Query log', DB::getQueryLog());
    //     }
    // }


    public function submit()
    {
        if (config('app.debug')) {
            DB::enableQueryLog();
        }

        Log::info('Package data before submission', $this->package);

        if (!isset($this->package['id']) || !isset($this->package['loan_required_amount'])) {
            $this->addError('package', 'Invalid loan package configuration');
            Log::error('Invalid package structure', $this->package);
            session()->flash('error', 'Invalid loan package configuration.');
            return;
        }

        Log::info('Submit method triggered');

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
            Log::info('Existing loan check: ' . ($existingLoan ? 'Exists' : 'None'));

            if ($existingLoan) {
                Log::info('Existing loan found - aborting');
                $this->hasExistingLoan = true; // Set flag
                session()->flash('error', 'You already have an approved loan. Please complete or cancel your existing loan before applying for a new one.');
                return; // Stay on the form
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
                'cylinder_type_id' => $this->package['id'],
                'status' => 'pending',
                'payment_type' => 'loan',
            ]);
            Log::info('Installation created: ' . $installation->id);

            Log::info('Creating loan');
            $loan = Loan::create([
                'user_id' => $user->id,
                'installation_id' => $installation->id,
                'loan_required_amount' => (float) str_replace(',', '', $this->package['loan_required_amount']),
            ]);
            Log::info('Loan created: ' . $loan->id);

            DB::commit();
            Log::info('Database transaction committed');

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
        ]);
    }
}
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\LoanPackage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Loan;
use Exception;
use Illuminate\Support\Facades\Log;


class LoanPackages extends Component
{
    public array $selectedLoanPackage = [];
    public $bajajiLoanPackage = [
        'loan_required_amount' => 1600000,
        'id' => 1,

    ];
    public $smallCarLoanPackage = [
        'loan_required_amount' => 1900000,
        'id' => 2,

    ];

    public $mediumCarLoanPackage = [
        'loan_required_amount' => 2200000,
        'id' => 3,

    ];


    public bool $showApplicationForm = false;


    public function setPackage($package)
{
    // Ensure package keys are strings
    $loanPackages = [
            'bajaji' => [
                'id' => 1,
                'loan_required_amount' => '1,600,000'
            ],
            'small_car' => [
                'id' => 2,
                'loan_required_amount' => '1,900,000'
            ],
            'medium_car' => [
                'id' => 3,
                'loan_required_amount' => '2,200,000'
            ],
    ];

    // Add validation
    if (!is_string($package)) {
        throw new \InvalidArgumentException('Package key must be a string');
    }

    if (!array_key_exists($package, $loanPackages)) {
        throw new \InvalidArgumentException("Invalid package: {$package}");
    }

    $this->selectedLoanPackage = $loanPackages[$package];
    $this->showApplicationForm = true;
}
    public function layout()
    {
        return 'base.app';
    }


    public function render()
    {

        return view('livewire.loan-packages', ['showApplicationForm' => $this->showApplicationForm]);
    }
}

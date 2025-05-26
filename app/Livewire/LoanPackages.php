<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LoanPackage;

class LoanPackages extends Component
{
    public $showApplicationForm = false;
    public $selectedLoanPackageId = null;

    public function setPackage($packageType)
    {
        // Map package type to LoanPackage ID
        $packageMap = [
            'bajaji' => 1, // Basic Bajaji Package (7L)
            'small_car' => 2, // Standard Bajaji Package (11L)
            'medium_car' => 3, // Premium Bajaji Package (15L)
        ];

        if (!isset($packageMap[$packageType])) {
            session()->flash('error', 'Invalid package selected.');
            return;
        }

        $this->selectedLoanPackageId = $packageMap[$packageType];
        $this->showApplicationForm = true;
    }

    public function render()
    {
        return view('livewire.loan-packages');
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LoanPackage;

class PricingCard extends Component
{
    public $loanPackages;
    public function render()
    {
        $this->loanPackages = LoanPackage::all();
        $loanPackages = response()->json($this->loanPackages);
        return view('livewire.pricing-card', [ 'jsonLoanPackages' => $loanPackages]);
    }
}

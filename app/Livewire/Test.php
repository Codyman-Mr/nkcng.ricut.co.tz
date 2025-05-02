<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\Installation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Test extends Component
{

    public function render()
    {
        $message = '';

        return view('livewire.test', [
            'message' => $message,
        ]);
    }
}

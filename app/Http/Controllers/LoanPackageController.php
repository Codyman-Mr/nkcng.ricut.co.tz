<?php


namespace App\Http\Controllers;
use Livewire\Component;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\User;
use App\Models\Installation;


class LoanPackageController extends Controller
{


    public function index()
    {
        return view('loan.loan-packages');
    }
}

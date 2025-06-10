<?php


namespace App\Http\Controllers;
use Livewire\Component;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\User;
use App\Models\Installation;


class TestingController extends Controller{

    public function testRedirect()
    {
        return redirect('/');
    }

    public function index(){
        $loans = Loan::all();
        return view('misc.testing', compact('loans'));
    }
}

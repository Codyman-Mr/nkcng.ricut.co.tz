<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Reports extends Controller
{
    //
    public function index()
    {
        $payment_report = Payment::all();
        return view('report.daily', compact('payment'));
    }
    public function filter(Request $request){
      $start_date=$request->start_date;
      $end_date=$request->end_date;

      $payment_report=Payment::whereDate('created_at','>=',$start_date)
                         ->whereDate('created_at','=<',$end_date)
                         ->get();
      return view('report.daily',compact('payment'));

                         

    }


     
}



@extends('layouts.app')
@section('title','Home')

@section('content')
@if(Auth::user()->role=='admin')

<main class="content px-3 py-4">
    <div class="container-fluid">
      <div class="mb-3">
        <h3 class="fw-bold fs-4 mb-3">Hello {{Auth::user()->first_name}} {{Auth::user()->last_name}},</h3>
   
  <form class="form-inline"   action="/filter"  method="GET"  >
  <div class="form-group mb-2" style="margin: left 200px;">
  <label >Start Date</label>
  <input type="date" class="form-control" id="inputPassword2" value="{{$start_date}}"  name="start_date" placeholder="Password">
  </div>
  <div class="form-group mx-sm-3 mb-2">
    <label >End Date</label>
    <input type="date" class="form-control" id="inputPassword2" value="{{$end_date}}"  name="end_date" placeholder="Password">
  </div>
  <button type="submit" class="btn btn-primary mb-2">filter</button>
</form>
      
        <div class="row">
          <div class="col-12">
                
                <table class="table table-striped text-sm" style="width: 100%;">
                    <thead class="text-xs bg-gray-50">
                      <tr>
                      
                        <th scope="col" class="px-6 py-3" style="width: 17rem; font-weight:600;">Customer </th>
                        <th scope="col" class="px-6 py-3" style="width: 17rem; font-weight:600;">Date of Payment</th>
                        <th scope="col" class="px-6 py-3" style="width: 15rem; font-weight:600;">Amount Paid</th>
                        <th scope="col" class="px-6 py-3 text-left" style="width: 10rem; font-weight:600;">Payment Method</th>
                      </tr>
                    </thead>

                    <tbody>
                        @foreach ($payment_report as $payment)
                            <tr class="bg-white border-b cursor-pointer" style="cursor: pointer;">
                                <td class="py-4">{{\Carbon\Carbon::parse($payment->loan?->user?->first_name)->format('d F Y')}}</td> 
                                <td class="py-4">{{\Carbon\Carbon::parse($payment->payment_date)->format('d F Y')}}</td> 
                                <td class="py-4">{{\Carbon\Carbon::parse($payment->payment_date)->format('d F Y')}}</td>
                                <td class="py-4">{{\Carbon\Carbon::parse($payment->payment_date)->format('d F Y')}}</td>
                                <td class="py-4">{{number_format($payment->paid_amount)}} Tsh</td>
                                <td class="py-4">{{Str::title($payment->payment_method)}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                    <p>No Record Found</p>
                @endunless
          </div>
        </div>
      </div>
    </div>
</main>
@endsection

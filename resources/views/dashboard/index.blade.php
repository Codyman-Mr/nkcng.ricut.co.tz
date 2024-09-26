@php
    $hideContainer = false;
    foreach ($user->loans as $loan) {
        if ($loan->status === 'pending') {
            $hideContainer = true;
            break;
        }
    }
@endphp


@extends('layouts.app')
@section('title','Home')

@section('content')
@if(Auth::user()->role=='customer')
<main class="content px-3 py-4">
    <div class="container-fluid {{ $hideContainer ? 'd-none' : '' }}">
      <div class="mb-3">
        <h3 class="fw-bold fs-4 mb-3">Hello {{Auth::user()->first_name}} {{Auth::user()->last_name}},</h3>
        <div class="row">
            @foreach ($user->loans as $loan)
            <div class="col-12 col-md-3">
                <div class="card border-0">
                    <div class="card-body py-4">
                        <h5 class="mb-2 fw-bold">Required Amount</h5>
                        <p class="mb-2 fw-bold" style="font-size: 1.5rem;">
                            {{ number_format($loan->loan_required_amount) }} Tshs
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="card border-0">
                    <div class="card-body py-4">
                        <h5 class="mb-2 fw-bold">Amount Paid</h5>
                        <p class="mb-2 fw-bold" style="font-size: 1.5rem;">
                            {{ number_format($loan->payments->sum('paid_amount')) }} Tshs
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="card border-0">
                    <div class="card-body py-4">
                        <h5 class="mb-2 fw-bold">Remaining Amount</h5>
                        <p class="mb-2 fw-bold" style="font-size: 1.5rem;">
                            {{ number_format($loan->loan_required_amount - $loan->payments->sum('paid_amount')) }} Tshs
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="card border-0">
                    <div class="card-body py-4">
                        <h5 class="mb-2 fw-bold">Payment Progress</h5>
                        @php
                            $totalAmount = $loan->loan_required_amount;
                            $paidAmount = $loan->payments->sum('paid_amount');
                            $progressPercentage = $totalAmount > 0 ? ($paidAmount / $totalAmount) * 100 : 0;
                        @endphp
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: {{$progressPercentage}}%;" aria-valuenow="{{$progressPercentage}}" aria-valuemin="0" aria-valuemax="100">
                                {{ number_format($progressPercentage) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <br><br>
        <h3 class="fw-bold fs-4 my-3">Payment History</h3>

        <div class="row">
          <div class="col-12">
                @unless(count($payments)==0)
                <table class="table table-striped text-sm" style="width: 100%;">
                    <thead class="text-xs bg-gray-50">
                      <tr>
                        <th scope="col" class="px-6 py-3" style="width: 17rem; font-weight:600;">Cutomer Name</th>
                        <th scope="col" class="px-6 py-3" style="width: 17rem; font-weight:600;">Plate Number</th>
                        <th scope="col" class="px-6 py-3" style="width: 17rem; font-weight:600;">Date of Payment</th>
                        <th scope="col" class="px-6 py-3" style="width: 15rem; font-weight:600;">Amount Paid</th>
                        <th scope="col" class="px-6 py-3 text-left" style="width: 10rem; font-weight:600;">Payment Method</th>
                      </tr>
                    </thead>

                    <tbody>
                        @foreach ($payments as $payment)
                            @if ($payment->loan->user_id == Auth::id())
                                <tr class="bg-white border-b cursor-pointer" style="cursor: pointer;">
                                    <td scope="col" class="px-6 py-3" style="width: 17rem; font-weight:600;">{{\Carbon\Carbon::parse($payment->loan?->user?->first_name)->format('d F Y')}}</td>
                                    <td scope="col" class="px-6 py-3" style="width: 17rem; font-weight:600;">{{\Carbon\Carbon::parse($payment->loan?->user?->customer_vehicles->plate_number)->format('d F Y')}}</td>
                                    <td class="py-4">{{\Carbon\Carbon::parse($payment->payment_date)->format('d F Y')}}</td>
                                    <td class="py-4">{{number_format($payment->paid_amount)}} Tsh</td>
                                    <td class="py-4">{{Str::title($payment->payment_method)}}</td>
                                </tr>
                            @endif
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

    @if($hideContainer)
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Loan Application Status</h5>
                    <p class="card-text">Your loan application is still awaiting review. Please check back later for updates.</p>
                </div>
            </div>
        </div>
    @endif
</main>
@else
<main class="content px-3 py-4">
    <div class="container-fluid">
      <div class="mb-3">
        <h3 class="fw-bold fs-4 mb-3">Hello {{Auth::user()->first_name}} {{Auth::user()->last_name}},</h3>

        <div class="row my-2">
            <div class="col-12 col-md-3">
                <div class="card border-0">
                    <div class="card-body py-4">
                        <h5 class="mb-2 fw-bold">Total Amount</h5>
                        <p class="mb-2 fw-bold" style="font-size: 1.5rem;">
                            {{ number_format($loan->loan_required_amount) }} Tshs
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card border-0">
                    <div class="card-body py-4">
                        <h5 class="mb-2 fw-bold">Paid amount</h5>
                        <p class="mb-2 fw-bold" style="font-size: 1.5rem;">
                            {{ number_format($payments->sum('paid_amount')) }} Tshs
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card border-0">
                    <div class="card-body py-4">
                        <h5 class="mb-2 fw-bold">Due Amount</h5>
                        <p class="mb-2 fw-bold" style="font-size: 1.5rem;">
                            {{ number_format($loan->loan_required_amount-$payments->sum('paid_amount')) }} Tshs
                        </p>
                    </div>
                </div>
            </div>
        </div>      
        
        <div class="row my-2">
            <div class="col-12 col-md-3">
                <div class="card border-0">
                    <div class="card-body py-4">
                        <h5 class="mb-2 fw-bold">No of customers </h5>
                        <p class="mb-2 fw-bold" style="font-size: 1.5rem;">
                            {{ number_format($user->count()) }} People
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card border-0">
                    <div class="card-body py-4">
                        <h5 class="mb-2 fw-bold">Customers with due  </h5>
                        @php
                        $count=0;
                        foreach($user->loans as $use){ 
                             $totalAmount = $use->loan_required_amount;
                             $paidAmount = $use->payments->sum('paid_amount');
                             if($totalAmount> $paidAmount){
                                   $count=$count+1;
                             }
                             
                             
                            }
                        @endphp
                        <p class="mb-2 fw-bold" style="font-size: 1.5rem;">
                            {{ number_format($count)}} People
                        </p>
                    </div>
                </div>
            </div>
          <div class="col-12 col-md-3">
                <div class="card border-0">
                    <div class="card-body py-4">
                        <h5 class="mb-2 fw-bold">Fully paid customers</h5>
                        
                        @php
                        $full=0;
                        foreach($user->loans as $use){ 
                             $totalAmount = $use->loan_required_amount;
                             $paidAmount = $use->payments->sum('paid_amount');
                             if($totalAmount==$paidAmount){
                                   $full=$full+1;
                             }
                             
                        @endphp
                    
                        <p class="mb-2 fw-bold" style="font-size: 1.5rem;">
                            {{number_format($full)  }} People
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <br><br>
        <h3 class="fw-bold fs-4 my-3">Payments</h3>

        <div class="row">
          <div class="col-12">
                @unless(count($payments)==0)
                <table class="table table-striped text-sm" style="width: 100%;">
                    <thead class="text-xs bg-gray-50">
                      <tr>
                        <th scope="col" class="px-6 py-3" style="width: 17rem; font-weight:600;">Date of Payment</th>
                        <th scope="col" class="px-6 py-3" style="width: 15rem; font-weight:600;">Amount Paid</th>
                        <th scope="col" class="px-6 py-3 text-left" style="width: 10rem; font-weight:600;">Payment Method</th>
                      </tr>
                    </thead>

                    <tbody>
                        @foreach ($payments as $payment)
                            <tr class="bg-white border-b cursor-pointer" style="cursor: pointer;">
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
@endif
@endsection

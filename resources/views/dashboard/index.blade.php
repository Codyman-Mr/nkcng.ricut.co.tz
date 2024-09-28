@php
    $jumla = 0; // Initialize $jumla
    $hideContainer = false;
    foreach ($user->loans as $loan) {
        if ($loan->status === 'pending') {
            $jumla += $loan['loan_required_amount'];
            $hideContainer = true;
            break; // Consider using break only if you want the first pending loan
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
                            {{ number_format($totalLoanAmount) }} Tshs
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
                            {{ number_format($totalLoanAmount - $payments->sum('paid_amount')) }} Tshs

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
                        <h5 class="mb-2 fw-bold">Customers with Due Loans</h5>
                        @php
                        $count = 0;
            
                        // Loop through all users
                        foreach ($users as $user) { 
                            foreach ($user->loans as $loan) {
                                $totalAmount = $loan->loan_required_amount;
                                $paidAmount = $loan->payments->sum('paid_amount');
                                if ($totalAmount > $paidAmount) {
                                    $count++; // Increment count if there's a due loan
                                    break; // Break to avoid counting the same user multiple times
                                }
                            }
                        }
                        @endphp
                        <p class="mb-2 fw-bold" style="font-size: 1.5rem;">
                            {{ $count }} People
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-3">
                <div class="card border-0">
                    <div class="card-body py-4">
                        <h5 class="mb-2 fw-bold">Fully Paid Customers</h5>
                        
                        @php
                        $fullyPaidCount = 0;
            
                        // Iterate through all users to check their loans
                        foreach ($users as $user) {
                            foreach ($user->loans as $loan) {
                                $totalAmount = $loan->loan_required_amount;
                                $paidAmount = $loan->payments->sum('paid_amount');
            
                                // Check if the loan is fully paid
                                if ($paidAmount >= $totalAmount) {
                                    $fullyPaidCount++; // Increment count for loans with pending amount of 0
                                    break; // No need to check further loans for this user
                                }
                            }
                        }
                        @endphp
                        
                        <p class="mb-2 fw-bold" style="font-size: 1.5rem;">
                            {{ $fullyPaidCount }} People
                        </p>
                    </div>
                </div>
            </div>
                        </div>
            </div>
            
        </div>

        <br><br>
        <h3 class="fw-bold fs-4 my-3">payments this week</h3>

        <div class="row">
            <div class="col-12">
                <h5 class="mb-2 fw-bold">Customers with Near Loan End Date</h5>
                @if($nearEndLoans->isEmpty())
                    <p>No loans nearing end date.</p>
                @else
                    <table class="table table-striped text-sm" style="width: 100%;">
                        <thead class="text-xs bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3" style="width: 17rem; font-weight:600;">Name</th>
                                <th scope="col" class="px-6 py-3" style="width: 15rem; font-weight:600;">Required Amount</th>
                                <th scope="col" class="px-6 py-3" style="width: 15rem; font-weight:600;">Pending Amount</th>
                                <th scope="col" class="px-6 py-3" style="width: 10rem; font-weight:600;">Days Remaining</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($nearEndLoans as $loan)
                                <tr class="bg-white border-b">
                                    <td class="py-4">{{ $loan->user->first_name }} {{ $loan->user->last_name }}</td>
                                    <td class="py-4">{{ number_format($loan->loan_required_amount) }} Tsh</td>
                                    <td class="py-4">{{ number_format($loan->loan_required_amount - $loan->payments->sum('paid_amount')) }} Tsh</td>
                                    <td class="py-4">
                                        @php
                                            $daysRemaining = \Carbon\Carbon::now()->diffInDays($loan->loan_end_date);
                                        @endphp
                                        {{ $daysRemaining }} Days
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
        
        
        
        
      </div>
    </div>
    
</main>
@endif
@endsection

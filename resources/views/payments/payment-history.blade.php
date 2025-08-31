@extends('layouts.app')

@section('title', 'Payment History - ' . ($loan->applicant_name ?? 'Unknown'))

@section('main-content')
    <div class="p-6 bg-white text-gray-700 space-y-16">

        {{-- Payment History Section --}}
        <div>
            <h2 class="text-3xl font-bold mb-6 border-b-4 border-indigo-600 pb-3 text-indigo-700">Payment History</h2>

            <div class="overflow-auto border border-indigo-600 rounded-lg shadow-lg">
                <table class="min-w-full text-left text-base font-medium">
                    <thead class="bg-indigo-600 text-white uppercase tracking-wide">
                        <tr>
                            <th class="px-8 py-4">Paid Amount</th>
                            <th class="px-8 py-4">Payment Date</th>
                            <th class="px-8 py-4">Payment Method</th>
                            <!-- <th class="px-8 py-4">Provider</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($loan->payments as $payment)
                            <tr class="border-b border-indigo-200 hover:bg-indigo-50 transition-colors duration-300">
                                <td class="px-8 py-4 text-indigo-900 font-semibold">{{ number_format($payment->paid_amount, 2) }} TZS</td>
                                <td class="px-8 py-4">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                                <td class="px-8 py-4 capitalize">{{ $payment->payment_method }}</td>
                                <!-- <td class="px-8 py-4">{{ $payment->provider ?? 'N/A' }}</td> -->
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-6 text-center text-indigo-600 font-semibold">No Payments Made</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Personal Details Section --}}
        <div>
            <h2 class="text-3xl font-bold mb-6 border-b-4 border-green-600 pb-3 text-green-700">Personal Details</h2>

            <table class="min-w-full text-left text-base font-medium border border-green-600 rounded-lg shadow-lg">
                <tbody>
                    <tr class="border-b border-green-200 hover:bg-green-50 transition-colors duration-300">
                        <td class="px-8 py-4 font-semibold bg-green-100 w-1/3 text-green-900">Applicant Name</td>
                        <td class="px-8 py-4">{{ $loan->applicant_name ?? 'N/A' }}</td>
                    </tr>
                    <tr class="border-b border-green-200 hover:bg-green-50 transition-colors duration-300">
                        <td class="px-8 py-4 font-semibold bg-green-100 text-green-900">NIDA Number</td>
                        <td class="px-8 py-4">{{ $loan->nida_number ?? 'N/A' }}</td>
                    </tr>
                    <tr class="border-b border-green-200 hover:bg-green-50 transition-colors duration-300">
                        <td class="px-8 py-4 font-semibold bg-green-100 text-green-900">Loan Package</td>
                        <td class="px-8 py-4">{{ ucfirst($loan->loan_package ?? 'N/A') }}</td>
                    </tr>
                    <tr class="border-b border-green-200 hover:bg-green-50 transition-colors duration-300">
                        <td class="px-8 py-4 font-semibold bg-green-100 text-green-900">Cylinder Capacity</td>
                        <td class="px-8 py-4">{{ $loan->cylinder_capacity ?? 'N/A' }}</td>
                    </tr>
                    <tr class="border-b border-green-200 hover:bg-green-50 transition-colors duration-300">
                        <td class="px-8 py-4 font-semibold bg-green-100 text-green-900">Loan Payment Plan</td>
                        <td class="px-8 py-4">{{ ucfirst($loan->loan_payment_plan ?? 'N/A') }}</td>
                    </tr>
                    <tr class="border-b border-green-200 hover:bg-green-50 transition-colors duration-300">
                        <td class="px-8 py-4 font-semibold bg-green-100 text-green-900">Loan Required Amount</td>
                        <td class="px-8 py-4">{{ number_format($loan->loan_required_amount, 2) ?? 'N/A' }} TZS</td>
                    </tr>
                    <tr class="border-b border-green-200 hover:bg-green-50 transition-colors duration-300">
                        <td class="px-8 py-4 font-semibold bg-green-100 text-green-900">Applicant Phone Number</td>
                        <td class="px-8 py-4">{{ $loan->applicant_phone_number ?? 'N/A' }}</td>
                    </tr>
                    <tr class="border-b border-green-200 hover:bg-green-50 transition-colors duration-300">
                        <td class="px-8 py-4 font-semibold bg-green-100 text-green-900">Loan Start Date</td>
                        <td class="px-8 py-4">
                            {{ isset($loan->loan_start_date) ? \Carbon\Carbon::parse($loan->loan_start_date)->format('M d, Y') : 'N/A' }}
                        </td>
                    </tr>
                    <tr class="border-b border-green-200 hover:bg-green-50 transition-colors duration-300">
                        <td class="px-8 py-4 font-semibold bg-green-100 text-green-900">Loan End Date</td>
                        <td class="px-8 py-4">
                            {{ isset($loan->loan_end_date) ? \Carbon\Carbon::parse($loan->loan_end_date)->format('M d, Y') : 'N/A' }}
                        </td>
                    </tr>
                    <tr class="border-b border-green-200 hover:bg-green-50 transition-colors duration-300">
                        <td class="px-8 py-4 font-semibold bg-green-100 text-green-900">Loan Status</td>
                        <td class="px-8 py-4">{{ ucfirst($loan->status ?? 'N/A') }}</td>
                    </tr>
                    @if(!empty($loan->rejection_reason))
                    <tr class="hover:bg-green-50 transition-colors duration-300">
                        <td class="px-8 py-4 font-semibold bg-green-100 text-red-600">Rejection Reason</td>
                        <td class="px-8 py-4 text-red-600">{{ $loan->rejection_reason }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

    </div>
@endsection

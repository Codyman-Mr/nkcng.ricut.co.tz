@extends('layouts.app')
@section('title', 'Payment History' . ' - ' . $loan->installation->customerVehicle->user->first_name . ' ' .
    $loan->installation->customerVehicle->user->last_name)

@section('main-content')
    <div class="p-6 bg-white text-gray-700 space-y-10">

        <div>
            <h2 class="text-xl font-semibold mb-2">Payments Hitory</h2>
            <div class="overflow-auto border border-gray-300 rounded">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-700 text-white">
                        <tr>
                            <th class="text-left px-4 py-2">Paid Amount</th>
                            <th class="text-left px-4 py-2">Payment Date</th>
                            <th class="text-left px-4 py-2">Payment Method</th>
                            <th class="text-left px-4 py-2">Provider</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($loan['payments'] as $loan)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $loan->paid_amount }}</td>
                                <td class="px-4 py-2">{{ $loan->payment_date }}</td>
                                <td class="px-4 py-2">{{ $loan->payment_method }}</td>
                                <td class="px-4 py-2">{{ $loan->provider }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-2">No Payments Made</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

@endsection

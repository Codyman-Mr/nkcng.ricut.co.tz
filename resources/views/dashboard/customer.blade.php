@section('main-content')

    <div>
        @if ($loan)
            <div class="status-container">

                @if ($loan->status === 'approved')
                    <table class="table table-striped text-sm" style="width: 100%;">
                        <thead class="text-xs bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3" style="width: 17rem; font-weight:600;">Name</th>
                                <th scope="col" class="px-6 py-3" style="width: 17rem; font-weight:600;">Phone Number
                                </th>
                                <th scope="col" class="px-6 py-3" style="width: 17rem; font-weight:600;">Amount
                                    Loaned</th>
                                <th scope="col" class="px-6 py-3" style="width: 15rem; font-weight:600;">Amount Paid
                                </th>
                                <th scope="col" class="px-6 py-3" style="width: 15rem; font-weight:600;">Amount Remaining
                                </th>

                                <th scope='col' class="px-6 py-3" style="width: 15rem; font-weight:600;">Time Left
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($payments as $payment)
                                @if ($payment->loan->user_id == Auth::id())
                                    <tr class="bg-white border-b cursor-pointer" style="cursor: pointer;">
                                        <td scope="col" class="px-6 py-3" style="width: 17rem; font-weight:600;">
                                            {{-- {{ \Carbon\Carbon::parse($payment->loan?->user?->first_name)->format('d F Y') }} --}}
                                        </td>
                                        <td scope="col" class="px-6 py-3" style="width: 17rem; font-weight:600;">
                                            {{-- {{ \Carbon\Carbon::parse($payment->loan?->user?->customer_vehicles->plate_number)->format('d F Y') }} --}}
                                        </td>
                                        <td class="py-4">
                                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('d F Y') }}</td>
                                        <td class="py-4">{{ number_format($payment->paid_amount) }} Tsh</td>
                                        <td class="py-4">{{ Str::title($payment->payment_method) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                @elseif ($loan->status === 'pending')
                    <div class="flex flex-col items-center justify-center gap-4 mt-4">
                        <h3 class="text-3xl font-bold text-gray-900"> Your request is pending approval </h3>
                        <h3 class="text-3xl font-bold text-gray-900"> Please wait </h3>

                        <div class="h-full w-full">
                            <lottie-player src="{{ asset('lottie/pending-animation.json') }}" background="transparent"
                                speed="1" loop autoplay></lottie-player>
                        </div>
                    </div>
                @elseif ($loan->status === 'denied')
                    <div class="flex flex-col items-center justify-center gap-4 mt-4">
                        <h5 class="text-3xl font-bold text-gray-900"> Your request Has been Denied </h5>
                        <h5 class="text-3xl font-bold text-gray-900"> Please Contact our offices for more clarification
                        </h5>
                    </div>
                @elseif ($loan->status === 'none')
                    <span class="text-gray-500">No Status 🚫</span>
                @elseif ($loan->status === 'rejected')
                    <span class="text-gray-500">Your loan Application has been rejected </span>
                    <span>
                        Reason: {{ $loan->rejection_reason }}
                    </span>
                @else
                    <span class="text-gray-500">Unknown Status 🔍</span>
                @endif
            </div>
        @else
            <div class=" flex justify-center items-center bg-green-100  px-4 py-3 rounded-lg">
                <div class="flex flex-col items-center justify-center gap-4 mt-4">
                    <h3 class="text-3xl font-bold text-gray-900">You don't have a loan yet</h3>

                    <button type="button"
                        class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                        <a href="{{ route('loan-application') }}">Apply for a loan</a></button>
                </div>

            </div>
        @endif
    </div>



@endsection

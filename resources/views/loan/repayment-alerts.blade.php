@extends('layouts.app')
@section('title', 'Repayment Alerts')

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('main-content')
    <div class="container-fluid mt-2 mx-auto p-4">
        <div class="row">
            <div class="col-lg-12 p-0">
                <div class="ibox">
                    <div class="ibox-content table-responsive">
                        {{-- @unless (count($dueSoonLoans) == 0) --}}
                            {{-- <table id="repayments-table" class="table text-sm" style="width: 100%;">
                                <thead class="text-xs bg-gray-50">
                                    <tr>
                                        <th>Name</th>
                                        <th>Payment Plan</th>
                                        <th>Last Payment Date</th>
                                        <th>Next Due Date</th>
                                        <th>Reminder Message</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dueSoonLoans as $loan)
                                        <tr class="bg-white border-b">
                                            <td>{{ $loan->user->full_name }} ({{ $loan->user->phone_number }})</td>
                                            <td>{{ ucfirst($loan->payment_plan) }}</td>
                                            <td>{{ $loan->last_payment_date ? $loan->last_payment_date->format('Y-m-d') : 'N/A' }}
                                            </td>
                                            <td>{{ $loan->next_due_date->format('Y-m-d') }}</td>
                                            <td>
                                                Habari {{ $loan->user->first_name }},
                                                Kumbukumbu ya malipo yako ijayo ni tarehe
                                                {{ $loan->next_due_date->format('d/m/Y') }}.
                                                Tafadhali hakikisha unalipa kwa wakati ili kuepuka malipo ya ziada.
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table> --}}
                            <livewire:scheduled-reminder-manager/> 
                        {{-- @else
                            <div x-data x-init="lottie.loadAnimation({
                                container: $el,
                                renderer: 'svg',
                                loop: true,
                                autoplay: true,
                                path: '/animations/no-data.json' // download and place in public/animations
                            })" class="w-64 h-64 mx-auto my-6">
                            </div>
                            <p class="text-center text-gray-600 dark:text-gray-300 text-2xl">Nothing to display yet.</p>

                        @endunless --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

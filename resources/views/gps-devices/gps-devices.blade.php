@extends('layouts.app')
@section('title', 'Gps Devices - ' . config('app.name'))

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('main-content')
    {{-- main content container --}}
    <div class="container mx-auto p-4">
        {{-- container for grid  --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white p-6 rounded-md shadow-l border border-gray-300">
                <span class="flex justify-between items-baseline pb-1 border-b border-nkgreen">
                    <div class="text-sm font-bold text-[#374151] ">Total Devices</div>
                    <img src="{{ asset('svg/gps.svg') }}" alt="money stack" class="w-8 h-8 object-cover">
                </span>
                <div class="text-2xl fw-bold text-[#374151] py-6">
                    {{$deviceCount['total']}} <strong class="text-sm">dvcss</strong>
                </div>
            </div>
            <div class="bg-white p-6 rounded-md shadow-l border border-gray-300">
                <span class="flex justify-between items-baseline pb-1 border-b border-nkgreen">
                    <div class="text-sm font-bold text-[#374151] ">Assigned Devices</div>
                    <img src="{{ asset('svg/gps.svg') }}" alt="money stack" class="w-8 h-8 object-cover">
                </span>
                <div class="text-2xl fw-bold  text-[#374151] py-6">
                    {{$deviceCount['assigned']}} <strong class="text-sm">dvcs</strong>
                </div>
            </div>
            <div class="bg-white p-6 rounded-md shadow-l border border-gray-300">
                <span class="flex justify-between items-baseline pb-1 border-b border-nkgreen">
                    <div class="text-sm font-bold text-[#374151] ">Unassigned Devices</div>
                    <img src="{{ asset('svg/gps.svg') }}" alt="money stack" class="w-8 h-8 object-cover">
                </span>
                <div class="text-2xl fw-bold  text-[#374151] py-6">
                    {{$deviceCount['unassigned']}} <strong class="text-sm">dvcs</strong>
                </div>
            </div>
            <div class="bg-white p-6 rounded-md shadow-l border border-gray-300">
                <span class="flex justify-between items-baseline pb-1 border-b border-nkgreen">
                    <div class="text-sm font-bold text-[#374151] ">active Devices</div>
                    <img src="{{ asset('svg/gps.svg') }}" alt="money stack" class="w-8 h-8 object-cover">
                </span>
                <div class="text-2xl fw-bold  text-[#374151] py-6">
                    {{$deviceCount['active']}} <strong class="text-sm">dvcs</strong>
                </div>
            </div>
            <div class="bg-white p-6 rounded-md shadow-l border border-gray-300">
                <span class="flex justify-between items-baseline pb-1 border-b border-nkgreen">
                    <div class="text-sm font-bold text-[#374151] ">Inactive Devices</div>
                    <img src="{{ asset('svg/gps.svg') }}" alt="money stack" class="w-8 h-8 object-cover">
                </span>
                <div class="text-2xl fw-bold  text-[#374151] py-6">
                    {{$deviceCount['inactive']}} <strong class="text-sm">dvcs</strong>
                </div>
            </div>
            <div class="bg-white p-6 rounded-md shadow-l border border-gray-300">
                <span class="flex justify-between items-baseline pb-1 border-b border-nkgreen">
                    <div class="text-sm font-bold text-[#374151] ">Unknown-status Devices</div>
                    <img src="{{ asset('svg/gps.svg') }}" alt="money stack" class="w-8 h-8 object-cover">
                </span>
                <div class="text-2xl fw-bold  text-[#374151] py-6">
                    43 <strong class="text-sm">dvcs</strong>
                </div>
            </div>
        </div>


        {{-- container for table --}}
        <div class="overflow-auto mx-auto p-auto">
            <livewire:gps-device-table-component />
        </div>
    </div>

@endsection

@extends('layouts.app')
@section('title', 'Installations' . ' - ' . config('app.name'))

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('main-content')

    <div class=" conatiner mx-auto p-4">
        {{-- container for grid  --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white p-6 rounded-md shadow-l border border-gray-300">
                <span class="flex justify-between items-baseline pb-1 border-b border-nkgreen">
                    <div class="text-sm font-bold text-[#374151] ">Total Installations</div>
                    <img src="{{ asset('svg/gps.svg') }}" alt="money stack" class="w-8 h-8 object-cover">
                </span>
                <div class="text-2xl fw-bold text-[#374151] py-6">
                    {{ $installationCount['total'] }} <strong class="text-sm">installations</strong>
                </div>
            </div>
            <div class="bg-white p-6 rounded-md shadow-l border border-gray-300">
                <span class="flex justify-between items-baseline pb-1 border-b border-nkgreen">
                    <div class="text-sm font-bold text-[#374151] ">pending Installations</div>
                    <img src="{{ asset('svg/gps.svg') }}" alt="money stack" class="w-8 h-8 object-cover">
                </span>
                <div class="text-2xl fw-bold  text-[#374151] py-6">
                    {{ $installationCount['pending'] }} <strong class="text-sm">installations</strong>
                </div>
            </div>
            <div class="bg-white p-6 rounded-md shadow-l border border-gray-300">
                <span class="flex justify-between items-baseline pb-1 border-b border-nkgreen">
                    <div class="text-sm font-bold text-[#374151] ">Completed Installations</div>
                    <img src="{{ asset('svg/gps.svg') }}" alt="money stack" class="w-8 h-8 object-cover">
                </span>
                <div class="text-2xl fw-bold  text-[#374151] py-6">
                    {{ $installationCount['completed'] }} <strong class="text-sm">installations</strong>
                </div>
            </div>
        </div>

        {{-- table for installations --}}
        <div class="overflow-auto mx-auto p-auto mt-10">


            <livewire:installations-table-component />


        </div>

    @endsection

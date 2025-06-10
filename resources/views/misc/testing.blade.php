@extends('layouts.app')
@section('title', 'Testing')


@section('main-content')
    <div class="flex flex-col justify-center items-center overflow-x-hiddeb m-6 p-8">


        {{--
        <livewire:test-msg /> --}}





        {{-- <livewire:user-location-tracker :user-id="1" /> --}}





        {{--
        @foreach ($users as $user)
            <div>{{ $user->name }}</div>
        @endforeach --}}

        {{-- <livewire:testing-component /> --}}

        {{-- <div x-data="{ open: false }" @click="open = !open">
            <div @click="open = true">Show Tracker</div>
            <div x-show="open">
                <livewire:user-location-tracker :user-id="$user->id" />
            </div>
        </div> --}}


        <div class="grid grid-cols-1 md:grid-cols-[200px_1fr] grid-rows-[auto_auto] gap-2 bg-red-500 mt-4">

            <!-- Left column -->
            <div class="row-start-1 col-start-1 bg-green-300 p-4">
                <livewire:send-sms />
            </div>
            <div class="row-start-2 col-start-1 bg-blue-300 p-4">4</div>

            <!-- Right large cell (spans two rows) -->
            <div class="row-span-2 col-start-2 grid grid-cols-2 gap-2 bg-yellow-300 p-4">
                <div class="flex flex-col items-center justify-center">
                    <h1 class="text-2xl font-bold mb-4">Multi Location Tracker</h1>
                    <p class="text-gray-600 mb-4">Click on the map to get the location of the device.</p>

                    {{-- <livewire:multi-location-tracker :devices="['1', '2', '3', '4', '5']" /> --}}

                    <livewire:multi-location-tracker :devices="['1', '2', '3', '4', '5']" />

                </div>
            </div>

        </div>

        @foreach ($loans as $loan)
          <div class="bg-white p-4 rounded shadow mb-4">
            <h2 class="text-lg font-semibold mb-2">{{ $loan->user->formalname }}</h2>
            <p class="text-gray-600">Loan ID: {{ $loan->id }}</p>
            <p class="text-gray-600">Amount: TZS {{ number_format($loan->loan_required_amount) }}</p>
            <p class="text-gray-600">Status: {{ ucfirst($loan->status) }}</p>
            <p class="text-gray-600">installation id: {{ ucfirst($loan->installation_id) }}</p>
            <p class="text-gray-600">Status: {{ ucfirst($loan->installation->status) }}</p>


          </div>

        @endforeach



    </div>




@endsection

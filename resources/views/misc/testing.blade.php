@extends('layouts.app')
@section('title', 'Testing')


@section('main-content')
    <div class="flex flex-col justify-center items-center overflow-x-hiddeb m-6 p-8">


{{--
        <livewire:test-msg /> --}}

        <livewire:send-sms />



        {{-- <livewire:user-location-tracker :user-id="1" /> --}}

        <div class="flex flex-col items-center justify-center">
            <h1 class="text-2xl font-bold mb-4">Multi Location Tracker</h1>
            <p class="text-gray-600 mb-4">Click on the map to get the location of the device.</p>

            {{-- <livewire:multi-location-tracker :devices="['1', '2', '3', '4', '5']" /> --}}

            <livewire:multi-location-tracker :devices="['1', '2', '3', '4', '5']" />

        </div>



{{--
        @foreach ($users as $user)
            <div>{{ $user->name }}</div>
        @endforeach --}}

        <livewire:testing-component />

        {{-- <div x-data="{ open: false }" @click="open = !open">
            <div @click="open = true">Show Tracker</div>
            <div x-show="open">
                <livewire:user-location-tracker :user-id="$user->id" />
            </div>
        </div> --}}




    </div>




@endsection

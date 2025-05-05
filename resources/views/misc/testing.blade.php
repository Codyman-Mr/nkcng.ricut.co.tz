@extends('layouts.app')
@section('title', 'Testing')


@section('main-content')
    <div class="flex flex-col justify-center items-center overflow-x-hiddeb mb-6">



        {{-- <livewire:test-msg />



        <div>
            <livewire:pricing-card accesskey="1" />
        </div>

        <button type="button" class="btn btn-accent " onclick=testingRedirect()>
            testing redirect
        </button>



        <!-- Add this in your layout if Alpine.js isn't loaded -->
        <script src="//unpkg.com/alpinejs" defer></script>
 --}}

        {{-- <livewire:user-location-tracker :user-id="1" /> --}}

        <livewire:multi-location-tracker :devices="['1', '2', '3', '4', '5']" />

        {{-- <div x-data="{ open: false }" @click="open = !open">
            <div @click="open = true">Show Tracker</div>
            <div x-show="open">
                <livewire:user-location-tracker :user-id="$user->id" />
            </div>
        </div> --}}




    </div>




@endsection




@extends('layouts.app')
@section('title', 'Pending Loan' . ' - ' . $loan->installation->customerVehicle->user->first_name . ' ' .
    $loan->installation->customerVehicle->user->last_name)

    <meta name="csrf-token" content="{{ csrf_token() }}">

@section('main-content')

    <div class="max-w-6xl mx-auto p-6 space-y-6">


        <livewire:loan-approval :loan="$loan" />

        @if ($errors->any())
            <div class="mt-4 bg-red-100 text-red-700 p-4 rounded-md">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


    </div>




@endsection

@extends('layouts.app')

@section('title', 'Loan Application')

@section('main-content')

    {{-- Conditional rendering based on consent --}}
    @if(!session()->has('user_consent'))
        @livewire('consent-modal')
    @else
        {{-- @livewire('loan-application-form') --}}
        @livewire('loan-packages')
    @endif

@endsection


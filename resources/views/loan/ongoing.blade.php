@extends('layouts.app')
@section('title', 'Ongoing Loans')

@php
    use Carbon\Carbon;
    use Carbon\CarbonInterface;
@endphp




<meta name="csrf-token" content="{{ csrf_token() }}">

@section('main-content')



{{-- @livewire('loan-table-component') --}}

<livewire:loan-table-component />

{{-- @livewire('test-counter') --}}

@endsection

@extends('layouts.app')
@section('title', 'Pending Loans')

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('main-content')

    <livewire:pending-loans-table />
@endsection

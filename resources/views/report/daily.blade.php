@extends('layouts.app')
@section('title', 'Reports')

@section('main-content')
    @if (Auth::user()->role == 'admin')
       <livewire:reports-component />
    @else
        <h2>Reports for Users</h2>
    @endif
@endsection

@extends('layouts.app')
@section('title', 'Reports')

@section('main-content')
    @if (Auth::user()->role == 'admin')
        <livewire:test />
    @else
        <h2>Reports for Users</h2>
    @endif
@endsection

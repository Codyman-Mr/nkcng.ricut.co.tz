@extends('layouts.app')
@section('title', 'Home')

@php
    $jumla = 0; // Initialize $jumla
    $hideContainer = false;
    foreach ($user->loans as $loan) {
        if ($loan->status === 'pending') {
            $jumla += $loan['loan_required_amount'];
            $hideContainer = true;
            break; // Consider using break only if you want the first pending loan
        }
    }
@endphp





@auth
    {{-- Check if the user is an admin --}}
    @if (auth()->user()->role === 'admin')
        {{-- Render the admin dashboard --}}
        @include('dashboard.admin')

        {{-- Check if the user is a customer --}}
    @elseif(auth()->user()->role === 'customer')
        {{-- Render the customer dashboard --}}
        @include('dashboard.customer')

        {{-- Fallback for other roles or no role --}}
    @else
        <p>You do not have access to any dashboard.</p>
    @endif
@else
    <p>Please log in to access the dashboard.</p>
@endauth

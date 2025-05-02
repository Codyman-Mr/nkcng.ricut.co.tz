@extends('layouts.app')
@section('title', 'Users - ' . config('app.name'))

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('main-content')

   <livewire:user-index />

@endsection

@extends('layouts.app')
@section('title', 'Gps Devices - ' . 'assign-device')

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('main-content')

<livewire:assign-gps-device :userId="$userId"/>

@endsection

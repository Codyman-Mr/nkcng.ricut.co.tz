@extends('layouts.app')
@section('title', 'Repayment Alerts')

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('main-content')
    <div class="container-fluid mt-2 mx-auto p-4">
        <div class="row">
            <div class="col-lg-12 p-0">
                <div class="ibox">
                    <div class="ibox-content table-responsive">
                        

                            <livewire:repayment-alerts-view />

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

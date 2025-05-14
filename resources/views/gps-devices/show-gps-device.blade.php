@extends('layouts.app')
@section('title', 'Gps Device - ' . $device->device_id  )
@section('description', 'Gps Device - ' . config('app.name'))
@section('keywords', 'Gps Device - ' . config('app.name'))

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('main-content')

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">GPS Device Details</h2>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <span class="w-1/3 font-medium text-gray-700 dark:text-gray-300">Device ID:</span>
                            <span class="text-gray-900 dark:text-white">{{ucfirst($device->device_id)  }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-1/3 font-medium text-gray-700 dark:text-gray-300">Activity Status:</span>
                            <span class="text-gray-900 dark:text-white">{{ Str::title($device->activity_status) }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-1/3 font-medium text-gray-700 dark:text-gray-300">Assignment Status:</span>
                            <span class="text-gray-900 dark:text-white">{{ Str::title($device->assignment_status) }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-1/3 font-medium text-gray-700 dark:text-gray-300">Assigned To:</span>
                            <span class="text-gray-900 dark:text-white">{{ $device->assignedTo->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-1/3 font-medium text-gray-700 dark:text-gray-300">Power Status:</span>
                            <span class="text-gray-900 dark:text-white">{{ Str::title($device->power_status) }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-1/3 font-medium text-gray-700 dark:text-gray-300">Last Power Update:</span>
                            <span class="text-gray-900 dark:text-white">{{ $device->power_status_updated_at?->format('M d, Y H:i') ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-1/3 font-medium text-gray-700 dark:text-gray-300">Unassigned Reason:</span>
                            <span class="text-gray-900 dark:text-white">{{ $device->unassigned_reason ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('gps-devices') }}" class="btn btn-sm btn-outline-secondary text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            Back to Devices
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

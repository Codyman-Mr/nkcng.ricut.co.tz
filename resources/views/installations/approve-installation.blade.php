@php
    use Illuminate\Support\Str;
@endphp

@extends('layouts.app')
@section('title', 'Approve Installation' . ' - ' . $installation->id)

@section('main-content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 flex justify-center ">
                <div class="ibox   bg-nkgreen shadow-md sm:rounded-lg w-full flex flex-col justify-center">
                    <div
                        class="ibox-content w-1/2 bg-white  dark:bg-gray-800 shadow-md sm:rounded-lg p-6 flex flex-col justify-center">
                        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Approve Installation</h2>

                        @if (session('success'))
                            <div
                                class="mb-4 p-4 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 rounded-lg">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if ($installation)
                            @if ($installation->status === 'completed')
                                <div
                                    class="mb-4 p-4 bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300 rounded-lg">
                                    This installation has already been completed.
                                </div>
                            @elseif ($installation->status === 'pending')
                                <div class="mt-4">
                                    <div class="space-y-4 flex flex-col  justify-center">
                                        <div class="flex items-center ">
                                            <span class="w-1/3 font-medium text-gray-700 dark:text-gray-300">Installation
                                                ID:</span>
                                            <span class="text-gray-900 dark:text-white">{{ $installation->id }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="w-1/3 font-medium text-gray-700 dark:text-gray-300">Vehicle:</span>
                                            <span
                                                class="text-gray-900 dark:text-white">{{ $installation->customervehicle->plate_number ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="w-1/3 font-medium text-gray-700 dark:text-gray-300">Cylinder
                                                Type:</span>
                                            <span
                                                class="text-gray-900 dark:text-white">{{ $installation->cylinderType->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="w-1/3 font-medium text-gray-700 dark:text-gray-300">Status:</span>
                                            <span
                                                class="text-gray-900 dark:text-white">{{ Str::title($installation->status) }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="w-1/3 font-medium text-gray-700 dark:text-gray-300">Payment
                                                Type:</span>
                                            <span
                                                class="text-gray-900 dark:text-white">{{ Str::title($installation->payment_type) }}</span>
                                        </div>
                                    </div>

                                    <form action="{{ route('approve-installation.update', $installation->id) }}"
                                        method="POST" class="mt-6 space-y-4">
                                        @csrf
                                        @method('POST')

                                        <div>
                                            <label for="status"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Installation
                                                Status</label>
                                            <select name="status" id="status"
                                                class="mt-1 block w-[40%] py-2 px-3 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm">
                                                <option value="pending"
                                                    {{ $installation->status === 'pending' ? 'selected' : '' }}>Pending
                                                </option>
                                                <option value="completed"
                                                    {{ $installation->status === 'completed' ? 'selected' : '' }}>Completed
                                                </option>
                                            </select>
                                            @error('status')
                                                <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div>
                                            <button type="submit"
                                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                                Update Installation
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        @else
                            <p class="text-red-500 dark:text-red-400">Installation not found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

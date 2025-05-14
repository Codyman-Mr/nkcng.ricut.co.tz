<div class="grid grid-cols-1 auto-rows-auto gap-4 md:grid-cols-3 md:grid-rows-3 md:gap-4 h-full p-4">
    <!-- Sidebar (divs 1 & 4) -->
    <div class="bg-transparent md:col-span-1 md:row-span-3 flex items-center justify-center p-4">
        <div class="bg-white w-full h-full rounded-xl shadow-sm p-4 flex flex-col justify-between">
            <!-- Top Section: Title, Avatar, Info -->
            <div class="space-y-6">
                <!-- Title -->
                <h2 class="text-2xl font-bold text-gray-800 text-center">Personal Details</h2>

                <!-- Avatar with Edit Icon -->
                <div class="flex justify-center">
                    <div class="relative">
                        <!-- Avatar (replace initials with <img> if image exists) -->
                        <div
                            class="w-24 h-24 bg-gray-300 rounded-full flex items-center justify-center text-2xl font-bold text-white overflow-hidden">
                            JD
                        </div>

                        <!-- Edit Icon -->
                        <label for="avatar-upload"
                            class="absolute bottom-0 right-0 bg-gray-600 p-1 rounded-full cursor-pointer hover:bg-gray-700 transition">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M17.414 2.586a2 2 0 010 2.828L9 13.828l-4 1 1-4 8.414-8.414a2 2 0 012.828 0z" />
                            </svg>
                        </label>
                        <input id="avatar-upload" type="file" class="hidden" />
                    </div>
                </div>

                <!-- User Info Fields -->
                <div class="space-y-3 text-sm text-gray-800">
                    <div><span class="font-semibold text-gray-500">Name:</span>
                        {{ $user->first_name . ' ' . $user->last_name }}</div>
                    <div><span class="font-semibold text-gray-500">Phone:</span> {{ $user->phone_number }}</div>
                    <div><span class="font-semibold text-gray-500">Gender:</span> {{ $user->gender }}</div>
                    <div><span class="font-semibold text-gray-500">Date of Birth:</span> {{ $user->dob }}</div>
                    <div><span class="font-semibold text-gray-500">NIDA:</span> {{ $user->nida_number }}</div>
                    <div><span class="font-semibold text-gray-500">Address:</span> {{ $user->address }}</div>
                </div>
            </div>

            <!-- Edit Button at Bottom -->
            <button class="w-full bg-gray-600 text-white py-2 rounded-lg hover:bg-gray-700 transition">
                Edit Details
            </button>
        </div>

    </div>

    <!-- Map area (divs 2,3,5,6) -->
    <div class="bg-gray-300 md:col-span-2 md:row-span-2 flex items-center justify-center rounded-lg py-4">
        @if ($gpsDevice)
            <div class="w-full h-full ">
                <livewire:location-tracker :deviceId="$gpsDeviceId" />
            </div>
        @else
            <livewire:assign-gps-device :userId="$userId" />
        @endif
    </div>


    <!-- Remaining cells (7,8,9) -->

    <div class="bg-transparent flex items-center justify-center ">
        <div class="bg-white w-full  rounded-lg shadow-sm p-2 flex flex-col px-4 ">
            <!-- Loan Section: Title, Avatar, Info -->
            <div class="space-y-4">
                <!-- Title -->
                <h2 class="text-xl font-bold text-gray-800 text-start">Loan Details</h2>
            </div>

            @if (!$loan)
                <div class="space-y-1 text-sm text-gray-800 mt-3">
                    <div><span class="font-semibold text-gray-500">The user has not applied for a loan</span></div>
                </div>
            @elseif ($loan->status === 'approved')
                <div class="space-y-1 text-sm text-gray-800 mt-3">

                    <div><span class="font-semibold text-gray-500">Loan Status:</span> {{ $loan->status }}</div>
                    <div><span class="font-semibold text-gray-500">Loan Amount:</span> {{ $loan->loan_required_amount }}
                    </div>
                    <div><span class="font-semibold text-gray-500">Loan Start Date:</span> {{ $loan->loan_start_date }}
                    </div>
                    <div><span class="font-semibold text-gray-500">Loan End Date:</span> {{ $loan->loan_end_date }}
                    </div>
                </div>
            @elseif ($loan->status === 'pending')
                <div class="space-y-1 text-sm text-gray-800 mt-3">
                    <div><span class="font-semibold text-gray-500">Loan Pending Validation</span></div>
                    <button class="w-full bg-gray-600 text-white py-2 rounded-lg hover:bg-gray-700 transition"
                        type="button" wire:click=''> Validate </button>
                </div>
            @elseif ($loan->status === 'rejected')
                <div class="space-y-1 text-sm text-gray-800 mt-3">
                    <div><span class="font-semibold text-gray-500">Loan Rejected</span></div>
                    <div><span class="font-semibold text-gray-500">Loan Rejection Reason:</span>
                        {{ $loan->rejection_reason }}</div>

                </div>
            @else
                <div class="space-y-1 text-sm text-gray-800 mt-3">
                    <div><span class="font-semibold text-gray-500">The user has not applied for a loan</div>

                </div>
            @endif
        </div>
    </div>
    <div class="bg-transparent flex items-center justify-center ">
        <div class="bg-white w-full  rounded-lg shadow-sm p-2 flex flex-col px-4 justify-between">
            <!-- Vehicle Section: Title, Avatar, Info -->
            <div class="space-y-2">
                <!-- Title -->
                <h2 class="text-xl font-bold text-gray-800 ">Vehicle Details</h2>
            </div>
            @if (!$vehicle)
                <div class="space-y-1 text-xs text-gray-800 mt-3">
                    <div><span class="font-semibold text-gray-500">The user has not applied for a vehicle</span></div>
                </div>
            @else
                <div><span class="font-semibold text-gray-500">vehicle Status:</span> {{ $vehicleStatus }}</div>
                <div><span class="font-semibold text-gray-500">Vehicle Model:</span> {{ $vehicle->vehicle_model }}
                </div>

                <div><span class="font-semibold text-gray-500">Vehicle Number Plate:</span>
                    {{ $vehicle->vehicle_number_plate }}</div>
                <div><span class="font-semibold text-gray-500">Vehicle Type:</span> {{ $vehicle->vehicle_type }}</div>

                <div class="flex flex-rows justify-evenly w-full mt-2">

                    @if ($vehicleStatus === 'on')
                        <button class="w-full bg-red-700 text-white py-2 rounded-lg hover:bg-red-400 transition"
                            type="button" wire:click='toggleVehicleStatus()'> Turn Off </button>
                    @elseif ($vehicleStatus === 'off')
                        <button class="w-full bg-green-700 text-white py-2 rounded-lg hover:bg-green-400 transition"
                            type="button" wire:click='toggleVehicleStatus()'> Turn On </button>
                    @endif

                </div>
            @endif

        </div>
    </div>



</div>

<div x-data="{ currentStep: @entangle('currentStep') }" x-cloak class="max-w-3xl mx-auto py-8 px-4">
    <!-- Error Messages -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Success/Error Flash Messages -->
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Progress Indicator -->
    <div class="mb-8">
        <div class="flex justify-between items-center relative">
            <div class="absolute top-3 left-0 right-0 h-1 bg-gray-200"></div>
            @foreach ([1 => 'Personal', 2 => 'Vehicle', 3 => 'Guarantors'] as $step => $label)
                <div class="flex flex-col items-center z-10">
                    <div
                        class="w-10 h-10 mb-2 rounded-full flex items-center justify-center
                            @if ($currentStep > $step) bg-green-500 text-white
                            @elseif($currentStep == $step) bg-blue-600 text-white
                            @else bg-gray-200 @endif
                            transition-colors duration-300">
                        {{ $step }}
                    </div>
                    <span class="text-xs font-medium text-gray-600">{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <form wire:submit.prevent="submit" enctype="multipart/form-data" class="space-y-4">
        <!-- Personal Details Card -->
        <div x-show="currentStep === 1" x-transition:enter.duration.300ms
            class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h3 class="text-2xl font-semibold text-gray-800">Personal Details</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- First Name (Read-only from Auth::user()) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                    <input type="text" wire:model="first_name"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 bg-gray-100" >
                </div>

                <!-- Last Name (Read-only from Auth::user()) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                    <input type="text" wire:model="last_name"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 bg-gray-100" >
                </div>

                <!-- Phone Number (Read-only from Auth::user()) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="text" wire:model="phone_number"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 bg-gray-100" >
                </div>

                <!-- Date of Birth -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                    <input type="date" wire:model="dob"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('dob')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- NIDA Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">NIDA Number</label>
                    <input type="text" wire:model="nida_no"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('nida_no')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Address -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <input type="text" wire:model="address"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('address')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Gender Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                    <div class="flex gap-6">
                        <label class="inline-flex items-center space-x-2">
                            <input type="radio" wire:model="gender" value="male"
                                class="form-radio h-5 w-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="text-gray-700">Male</span>
                        </label>
                        <label class="inline-flex items-center space-x-2">
                            <input type="radio" wire:model="gender" value="female"
                                class="form-radio h-5 w-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="text-gray-700">Female</span>
                        </label>
                    </div>
                    @error('gender')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Vehicle Details Card -->
        <div x-show="currentStep === 2" x-transition:enter.duration.300ms
            class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h3 class="text-2xl font-semibold text-gray-800">Vehicle Details</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Vehicle Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle Name</label>
                    <input type="text" wire:model="vehicle_name"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('vehicle_name')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Vehicle Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle Type</label>
                    <select wire:model="vehicle_type"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select Type</option>
                        <option value="car">Car</option>
                        <option value="bajaj">Bajaj</option>
                    </select>
                    @error('vehicle_type')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Plate Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Plate Number</label>
                    <input type="text" wire:model="plate_number"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('plate_number')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Fuel Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fuel Type</label>
                    <select wire:model="fuel_type"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select Type</option>
                        <option value="petrol">Petrol</option>
                        <option value="diesel">Diesel</option>
                    </select>
                    @error('fuel_type')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Guarantor Details Card -->
        <div x-show="currentStep === 3" x-transition:enter.duration.300ms
            class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h3 class="text-2xl font-semibold text-gray-800">Guarantor from Local Government</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-2">
                <!-- Government Guarantor First Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                    <input type="text" wire:model="gvt_guarantor_first_name"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('gvt_guarantor_first_name')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Government Guarantor Last Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                    <input type="text" wire:model="gvt_guarantor_last_name"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('gvt_guarantor_last_name')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Government Guarantor Phone Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="text" wire:model="gvt_guarantor_phone_number"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('gvt_guarantor_phone_number')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Government Guarantor NIDA Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">NIDA Number</label>
                    <input type="text" wire:model="gvt_guarantor_nida_no"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('gvt_guarantor_nida_no')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="border-b border-gray-200 pb-1 mb-2 mt-6">
                <h3 class="text-2xl font-semibold text-gray-800">Guarantor with Permanent Employment Contract</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Private Guarantor First Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                    <input type="text" wire:model="private_guarantor_first_name"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('private_guarantor_first_name')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Private Guarantor Last Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                    <input type="text" wire:model="private_guarantor_last_name"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('private_guarantor_last_name')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Private Guarantor Phone Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="text" wire:model="private_guarantor_phone_number"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('private_guarantor_phone_number')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Private Guarantor NIDA Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">NIDA Number</label>
                    <input type="text" wire:model="private_guarantor_nida_no"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('private_guarantor_nida_no')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        {{-- <div class="flex justify-between mt-6">
            @if ($currentStep > 1)
                <button type="button" wire:click="previous"
                        class="px-6 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span>Previous</span>
                </button>
            @else
                <div></div>
            @endif

            @if ($currentStep < $totalSteps)
                <button type="button" wire:click="next"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2">
                    <span>Next</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            @else
                <button type="submit"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Submit Application</span>
                </button>
            @endif
        </div> --}}
        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show"
                class="alert alert-success bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 flex justify-between items-center">
                <span>{{ session('message') }}</span>
                <button @click="show = false" class="text-green-700 hover:text-green-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif
        @if (session()->has('error'))
            <div x-data="{ show: true }" x-show="show"
                class="alert alert-danger bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 flex justify-between items-center">
                <span>{!! session('error') !!}</span>
                <button @click="show = false" class="text-red-700 hover:text-red-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        <!-- Navigation Buttons -->
        <div class="flex justify-between mt-6">
            @if ($currentStep > 1)
                <button type="button" wire:click="previous"
                    class="px-6 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Previous</span>
                </button>
            @else
                <div></div>
            @endif

            @if ($currentStep < $totalSteps)
                <button type="button" wire:click="next"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2">
                    <span>Next</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            @else
                <button type="submit" wire:loading.attr="disabled" x-bind:disabled="$wire.hasExistingLoan"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center space-x-2
                       @if ($hasExistingLoan) opacity-50 cursor-not-allowed @endif">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Submit Application</span>
                </button>
            @endif
        </div>
    </form>
</div>

@push('scripts')
    @livewireScripts
@endpush

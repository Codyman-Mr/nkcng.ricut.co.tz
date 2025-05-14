<div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6 mt-6">
    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Assign GPS Device</h3>

    @if ($successMessage)
        <div class="mb-4 p-4 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 rounded-lg">
            {{ $successMessage }}
        </div>
    @endif

    @if ($errorMessage)
        <div class="mb-4 p-4 bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300 rounded-lg">
            {{ $errorMessage }}
        </div>
    @endif

    @if ($statusMessage)
        <div class="mb-4 p-4 bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300 rounded-lg">
            {{ $statusMessage }}
            @if ($installationPending)
                <a href="/approve-installation/{{ $installationId }}"
                    class="mt-2 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Approve Installation
                </a>
            @endif
        </div>
    @endif

    @if ($canAssign)
        <form wire:submit.prevent="assignDevice" class="space-y-4">
            <div>
                <label for="device" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select GPS Device</label>
                <select wire:model="selectedDeviceId" id="device"
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm">
                    <option value="">Select a device...</option>
                    @foreach ($devices as $deviceId => $label)
                        <option value="{{ $deviceId }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('selectedDeviceId') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="vehicle" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Vehicle (Optional)</label>
                <select wire:model="selectedVehicleId" id="vehicle"
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm">
                    <option value="">No vehicle selected</option>
                    @foreach ($vehicles as $vehicleId => $plateNumber)
                        <option value="{{ $vehicleId }}">{{ $plateNumber }}</option>
                    @endforeach
                </select>
                @error('selectedVehicleId')
                    <span class="text-red-500 dark:text-red-400 text-sm">{{ $errors->first('selectedVehicleId') }}</span>
                @enderror
            </div>

            <div>
                <button type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Assign Device
                </button>
            </div>
        </form>

        @if (empty($devices))
            <p class="mt-4 text-gray-500 dark:text-gray-400">No unassigned GPS devices available.</p>
        @endif
    @endif
</div>

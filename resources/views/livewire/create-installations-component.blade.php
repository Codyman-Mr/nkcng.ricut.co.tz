<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Create Installation</h2>

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

                    <form wire:submit.prevent="saveInstallation" class="space-y-4">
                        <div>
                            <label for="vehicle" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Vehicle</label>
                            <select wire:model="selectedVehicleId" id="vehicle"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm">
                                <option value="">Select a vehicle...</option>
                                @foreach ($vehicles as $vehicleId => $plateNumber)
                                    <option value="{{ $vehicleId }}">{{ $plateNumber }}</option>
                                @endforeach
                            </select>
                            @error('selectedVehicleId') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="cylinderType" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Cylinder Type</label>
                            <select wire:model="selectedCylinderTypeId" id="cylinderType"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm">
                                <option value="">Select a cylinder type...</option>
                                @foreach ($cylinderTypes as $cylinderTypeId => $name)
                                    <option value="{{ $cylinderTypeId }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('selectedCylinderTypeId') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Installation Status</label>
                            <select wire:model="status" id="status"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm">
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                            </select>
                            @error('status') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="paymentType" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Type</label>
                            <select wire:model="paymentType" id="paymentType"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm">
                                <option value="loan">Loan</option>
                                <option value="direct">Direct</option>
                            </select>
                            @error('paymentType') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Save Installation
                            </button>
                        </div>
                    </form>

                    @if (empty($vehicles))
                        <p class="mt-4 text-gray-500 dark:text-gray-400">No vehicles available for this user.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

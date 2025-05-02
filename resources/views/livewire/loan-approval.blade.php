<div>
    <div class="bg-white p-6 rounded shadow-sm">

        <!-- Flash messages -->
        @if (session()->has('message'))
            <div class="p-2 mb-4 text-green-700 bg-green-100 rounded">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="p-2 mb-4 text-red-700 bg-red-100 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Loan Approval Form -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-6">

            <!-- Customer Name -->
            <div>
                <label class="block text-sm font-medium text-gray-600">Vehicle Name <span
                        class="text-muted">(Model)</span></label>
                <input type="text" disabled class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                    value="{{ $loan->installation->customerVehicle->model }}">
            </div>

            <!-- Vehicle Type -->
            <div>
                <label class="block text-sm font-medium text-gray-600">Vehicle Type</label>
                <select disabled class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value=""></option>
                    <option value="car"
                        {{ $loan->installation->customerVehicle->vehicle_type == 'car' ? 'selected' : '' }}>Car</option>
                    <option value="bajaj"
                        {{ $loan->installation->customerVehicle->vehicle_type == 'bajaj' ? 'selected' : '' }}>Bajaj
                    </option>
                </select>
            </div>

            <!-- Cylinder Type -->
            <div>
                <label class="text-sm font-medium text-gray-600">Cylinder Type</label>
                <select wire:model="cylinder_type"
                    class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">Select cylinder type</option>
                    @foreach ($cylinders as $cylinder)
                        <option value="{{ $cylinder->id }}">{{ $cylinder->name }}</option>
                    @endforeach
                </select>
                @error('cylinder_type')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <!-- Plate Number -->
            <div>
                <label class="block text-sm font-medium text-gray-600">Plate Number</label>
                <input type="text" disabled class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                    value="{{ $loan->installation->customerVehicle->plate_number }}">
            </div>

            <!-- Fuel Type -->
            <div>
                <label class="block text-sm font-medium text-gray-600">Fuel Type</label>
                <select disabled class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value=""></option>
                    <option value="petrol"
                        {{ $loan->installation->customerVehicle->fuel_type == 'petrol' ? 'selected' : '' }}>Petrol
                    </option>
                    <option value="diesel"
                        {{ $loan->installation->customerVehicle->fuel_type == 'diesel' ? 'selected' : '' }}>Diesel
                    </option>
                </select>
            </div>

            <!-- Required Amount -->
            <div>
                <label class="text-sm font-medium text-gray-600">Required Amount</label>
                <input type="text" wire:model.defer="loan_required_amount"
                    class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                    placeholder="Eg. 500,000" />
                @error('loan_required_amount')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <!-- Payment Plan -->
            <div>
                <label class="text-sm font-medium text-gray-600">Payment Plan</label>
                <select wire:model="loan_payment_plan"
                    class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">Select a plan</option>
                    <option value="weekly">Weekly</option>
                    <option value="bi-weekly">Bi Weekly</option>
                    <option value="monthly">Monthly</option>
                </select>
                @error('loan_payment_plan')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <!-- Loan End Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Loan End Date</label>
                <input type="date" wire:model="loan_end_date"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('loan_end_date')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between gap-4">
            <button wire:click="openRejectionModal"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                Reject Loan
            </button>

            <button wire:click="approveLoan"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                Approve Loan
            </button>
        </div>

        <!-- Rejection Modal -->
        @if ($showRejectModal)
            <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
                    <h2 class="text-lg font-semibold mb-4">Reject Loan</h2>
                    <textarea wire:model.defer="rejection_reason" class="w-full border p-2 rounded-md focus:outline-none focus:ring"
                        rows="4" placeholder="Enter rejection reason..."></textarea>



                    <div class="mt-4 flex justify-end space-x-2">
                        <button wire:click="$set('showRejectModal', false)"
                            class="px-3 py-1 rounded-md bg-gray-300 hover:bg-gray-400">Cancel</button>
                        <button wire:click="rejectLoan"
                            class="px-3 py-1 rounded-md bg-red-600 text-white hover:bg-red-700">Confirm Reject</button>
                    </div>
                </div>
            </div>
        @endif

    </div>


</div>

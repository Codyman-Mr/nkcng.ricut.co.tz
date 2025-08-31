<div>
    <form wire:submit.prevent="uploadDocuments" enctype="multipart/form-data">
        <h3 class="text-lg font-semibold mb-4">Upload Documents</h3>
        @php
            $requiredDocs = [
                'mktaba_wa_mkopo' => 'Mktaba wa Mkopo',
                'kitambulisho_mwomba_mbele' => 'Kitambulisho cha Taifa cha Mwomba Mkopo (Mbele)',
                'kitambulisho_mdhamini_1_mbele' => 'Kitambulisho cha Taifa cha Mdhamini wa 1 (Mbele)',
                'kitambulisho_mdhamini_2_mbele' => 'Kitambulisho cha Taifa cha Mdhamini wa 2 (Mbele)',
                'leseni_mwomba' => 'Leseni ya Mwomba Mkopo',
                'kadi_ya_usafiri' => 'Kadi ya Chombo cha Usafiri',
                'barua_ya_utambulisho' => 'Barua ya Utambulisho kutoka Serikali za Mitaa',
            ];
        @endphp
        <div class="grid grid-cols-1 gap-4 mb-6">
            @foreach ($requiredDocs as $key => $label)
                @if (!in_array($key, $uploadedDocumentTypes))
                    <div>
                        <label for="{{ $key }}"
                            class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                        <input type="file" wire:model="documents.{{ $key }}" id="{{ $key }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error("documents.{$key}")
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
            @endforeach
            @if (empty(array_diff(array_keys($requiredDocs), $uploadedDocumentTypes)))
                <p class="text-green-600 text-sm">All required documents have been uploaded.</p>
            @endif
        </div>
        @if (!empty(array_diff(array_keys($requiredDocs), $uploadedDocumentTypes)))
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mb-6">Upload Documents</button>
        @endif
    </form>

    <form wire:submit.prevent="approveLoan">
        <div class="mb-4">
            <label for="cylinderType" class="block text-sm font-medium text-gray-700">Cylinder Type</label>
            <select wire:model="cylinderType" id="cylinderType"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="">Select Cylinder Type</option>
                @foreach ($cylinders as $cylinder)
                    <option value="{{ $cylinder->id }}">{{ $cylinder->name }}</option>
                @endforeach
            </select>
            @error('cylinderType')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="loanRequiredAmount" class="block text-sm font-medium text-gray-700">Loan Amount (TZS)</label>
            <input type="number" wire:model="loanRequiredAmount" id="loanRequiredAmount"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            @error('loanRequiredAmount')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="loanPaymentPlan" class="block text-sm font-medium text-gray-700">Payment Plan</label>
            <select wire:model="loanPaymentPlan" id="loanPaymentPlan"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="weekly">Weekly</option>
                <option value="monthly">Bi-weekly</option>
                <option value="quarterly">Monthly</option>
            </select>
            @error('loanPaymentPlan')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="loan_start_date" class="block text-sm font-medium text-gray-700">Loan Start Date</label>
            <input type="date" wire:model="loan_start_date" id="loan_start_date"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            @error('loan_start_date')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="loanEndDate" class="block text-sm font-medium text-gray-700">Loan End Date</label>
            <input type="date" wire:model="loanEndDate" id="loanEndDate"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            @error('loanEndDate')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="paymentAmount" class="block text-sm font-medium text-gray-700">Initial Payment Amount
                (TZS)</label>
            <input type="number" wire:model="paymentAmount" id="paymentAmount"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            @error('paymentAmount')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
       <!-- Payment Method Selection -->
        <div x-data="{ paymentMethod: 'Cash' }" class="mb-2">
            <label for="paymentMethod" class="block text-sm font-medium text-gray-700">Payment Method</label>
            <select wire:model="paymentMethod" id="paymentMethod" x-model="paymentMethod"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="Cash" selected>Cash</option>
                <option value="Mobile Money">Mobile Money</option>
            </select>
            @error('paymentMethod')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror

            <!-- Mobile Money Fields -->
            <div x-show="paymentMethod === 'Mobile Money'">
                <div class="my-4 ">
                    <label for="phoneNumber" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="text" wire:model="phoneNumber" id="phoneNumber"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('phoneNumber')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="provider" class="block text-sm font-medium text-gray-700">Payment Provider</label>
                    <select wire:model="provider" id="provider"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="Mpesa">Mpesa</option>
                        <option value="TigoPesa">TigoPesa</option>
                        <option value="AirtelMoney">AirtelMoney</option>
                        <option value="HaloPesa">HaloPesa</option>
                    </select>
                    @error('provider')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Cash Receipt Upload -->
            <div x-show="paymentMethod === 'Cash'" class="my-4">
                <label for="receipt" class="block text-sm font-medium text-gray-700">Upload Receipt</label>
                <input type="file" wire:model="receipt" id="receipt" accept="image/*,application/pdf"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('receipt')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex space-x-4 items-center">
            <button type="submit"
                class="bg-green-500 text-white px-4 py-2 rounded flex items-center justify-center"
                wire:loading.attr="disabled" wire:target="approveLoan">
                <span wire:loading.remove wire:target="approveLoan">Approve Loan</span>
                <svg wire:loading wire:target="approveLoan" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </button>

            <button type="button" wire:click="openRejectionModal"
                class="bg-red-500 text-white px-4 py-2 rounded">Reject Loan</button>

            <button type="button" wire:click="checkPaymentStatus"
                class="bg-blue-500 text-white px-4 py-2 rounded">Check Payment Status</button>
        </div>
    </form>

    @if ($showRejectModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-lg font-semibold mb-4">Reject Loan</h2>
                <form wire:submit.prevent="rejectLoan">
                    <div class="mb-4">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Rejection Reason</label>
                        <textarea wire:model="rejection_reason" id="rejection_reason"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        @error('rejection_reason')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex space-x-4">
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Confirm Rejection</button>
                        <button type="button" wire:click="$set('showRejectModal', false)"
                            class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if (session()->has('message'))
        <div class="mt-4 p-4 bg-green-100 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mt-4 p-4 bg-red-100 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            Echo.private('user.{{ auth()->id() }}')
                .listen('.payment.status.updated', (event) => {
                    window.alert(event.message);
                });
        });
    </script>
</div>

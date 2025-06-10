<div>
    @if ($preview)
        <div class="alert alert-info">
            Preview: {{ $this->previewLoanMessage($loanId ?? 0, $userId ?? 0) }}
        </div>
    @endif

    <div class="flex flex-col gap-y-4">
        <div>
            <label for="recipients" class="block text-sm font-medium text-gray-700">Recipients</label>
            <input type="text" wire:model="recipients" id="recipients" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Enter User IDs or phone numbers (+255######### or 07########), comma-separated">
            @error('recipients') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
            <textarea wire:model="message" id="message" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="4" placeholder="Enter your message or leave blank for loan reminder"></textarea>
            @error('message') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="loanId" class="block text-sm font-medium text-gray-700">Loan ID (optional)</label>
            <input type="number" wire:model="loanId" id="loanId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Enter Loan ID for reminder">
            @error('loanId') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <button wire:click="sendMessage" class="btn btn-primary">Send SMS</button>
    </div>
</div>

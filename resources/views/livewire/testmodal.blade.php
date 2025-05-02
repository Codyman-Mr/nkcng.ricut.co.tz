
<div>
    <!-- Modal Backdrop -->
    <div
    x-data="{ show: @entangle('showModal') }"
     x-show="show"
     class="fixed z-20 inset-0">
        <div class="fixed inset-0 bg-gray-500 opacity-60"></div>
        <div class="bg-white fixed inset-0 rounded-md shadow-lg m-auto max-w-2xl" style="max-height: 500px;">
            <!-- Modal Header -->
            <div class="bg-gray-200 p-2 border border-b-2 text-white border-gray-400">
                Header
            </div>
            <!-- Modal Body -->
            <div class="p-2 border border-b-2 text-white border-gray-400">
                Body
            </div>

            <div>
                <!-- accept -->
                <button class="p-2 bg-green-500 text-white rounded" wire:click="accept">
                    Accept
                </button>
            <!-- Close Button -->
                <button class="p-2 bg-rose-500 text-white rounded" wire:click="closeModal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<div>
    @auth
        <div class="space-y-4">
            <button
                wire:click="sendTestMessage"
                wire:loading.attr="disabled"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
            >
                <span wire:loading.remove>📨 Send Test Message</span>
                <span wire:loading>⏳ Sending...</span>
            </button>

            @if($showPreview)
                <div class="p-4 bg-gray-100 rounded">
                    <h3 class="font-bold mb-2">Message Preview:</h3>
                    <p>{{ $this->previewTestMessage() }}</p>
                </div>
            @endif
        </div>
    @else
        <div class="bg-yellow-100 p-4 rounded">
            🔒 You must be logged in to send test messages
        </div>
    @endauth


    <script>
        document.addEventListener('DOMContentLoaded', function()){
            window.addEventListener('notify', event => {
                alert(`${event.detail.type}: ${event.detail.message}`);
            })
        }
    </script>
</div>
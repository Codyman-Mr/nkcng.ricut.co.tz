<div>
    <div class="flex items-center justify-center m-4 mb-4">
        <input type="text" wire:model="message">
        <h2>{{ $message }}</h2>
    </div>

    <div>
        <button wire:click="sendMessage">Send Message</button>
    </div>
</div>

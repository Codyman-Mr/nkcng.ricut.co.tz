<div>
    {{-- resources/views/livewire/reports-page.blade.php --}}

    <div class=" p-10 flex 
     flex-col m-auto">
        <input class="w-full " wire:model.live="search">
        @foreach ($users as $user)
            <div class="flex flex-col">
                <span class="text-gray-900 dark:text-white">{{ $user->first_name }}</span>
            </div>

        @endforeach
    </div>

</div>

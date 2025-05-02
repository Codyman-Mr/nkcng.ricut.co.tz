<div>
   <div class="p-4">
    <h2 class="text-2xl font-bold mb-4">Scheduled SMS Reminders</h2>

    @if (session()->has('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif

    <div class="mb-4">
        <input
            wire:model.debounce.500ms="search"
            type="text"
            placeholder="Search by name or status"
            class="border border-gray-300 rounded px-3 py-2 w-full md:w-1/3"
        />
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">User</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Loan</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Message</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Scheduled At</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Status</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($reminders as $reminder)
                    <tr>
                        <td class="px-4 py-2 text-sm">{{ $reminder->user->name }}</td>
                        <td class="px-4 py-2 text-sm">#{{ $reminder->loan->id }}</td>
                        <td class="px-4 py-2 text-sm">
                            @if ($editingId === $reminder->id)
                                <input wire:model.defer="editMessage" class="w-full border rounded px-2 py-1" />
                            @else
                                {{ $reminder->message }}
                            @endif
                        </td>
                        <td class="px-4 py-2 text-sm">
                            @if ($editingId === $reminder->id)
                                <input wire:model.defer="editScheduledAt" type="datetime-local" class="w-full border rounded px-2 py-1" />
                            @else
                                {{ $reminder->scheduled_at->format('d/m/Y H:i') }}
                            @endif
                        </td>
                        <td class="px-4 py-2 text-sm">{{ $reminder->status }}</td>
                        <td class="px-4 py-2 text-sm">
                            @if ($editingId === $reminder->id)
                                <button wire:click="updateReminder" class="bg-green-500 text-white px-3 py-1 rounded">Save</button>
                            @else
                                <button wire:click="edit({{ $reminder->id }})" class="bg-blue-500 text-white px-3 py-1 rounded">Edit</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $reminders->links() }}
    </div>
</div>
</div>

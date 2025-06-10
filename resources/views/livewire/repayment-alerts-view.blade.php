<div class="p-6 bg-white text-gray-700 space-y-10">

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-4">
        <div class="flex-1">
            <label class="block text-sm mb-1">Search Customer</label>
            <input type="text" wire:model.debounce.500ms="search" placeholder="Enter customer name"
                class="border border-gray-300 px-3 py-1 rounded w-full bg-white text-gray-700">
        </div>

        <div>
            <label class="block text-sm mb-1">Payment Plan</label>
            <select wire:model="paymentPlan" class="border border-gray-300 px-3 py-1 rounded bg-white text-gray-700">
                <option value="">All</option>
                <option value="weekly">Weekly</option>
                <option value="bi-weekly">Bi-Weekly</option>
                <option value="monthly">Monthly</option>
            </select>
        </div>

        <div>
            <label class="block text-sm mb-1">Reminder Type</label>
            <select wire:model="reminderType" class="border border-gray-300 px-3 py-1 rounded bg-white text-gray-700">
                <option value="">All</option>
                <option value="before">Before Due</option>
                <option value="on">On Due Date</option>
                <option value="after">After Due Date</option>
            </select>
        </div>

        <div>
            <label class="block text-sm mb-1">Reminder Date</label>
            <input type="date" wire:model="filterDate"
                class="border border-gray-300 px-3 py-1 rounded bg-white text-gray-700">
        </div>
    </div>

    {{-- Upcoming Repayments --}}
    <div>
        <h2 class="text-xl font-semibold mb-2">Upcoming Repayments (Next 7 Days)</h2>
        <div class="overflow-auto border border-gray-300 rounded">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-700 text-white">
                    <tr>
                        <th class="text-left px-4 py-2">Customer</th>
                        <th class="text-left px-4 py-2">Due Date</th>
                        <th class="text-left px-4 py-2">Amount Due</th>
                        <th class="text-left px-4 py-2">Reminders Sent</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($upcomingRepayments as $loan)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $loan->user->formalname }}</td>
                            <td class="px-4 py-2">{{ $loan->due_date->toDateString() }}</td>
                            <td class="px-4 py-2">{{ number_format($loan->amount_due, 2) }}</td>
                            <td class="px-4 py-2">
                                @foreach (['before', 'on', 'after'] as $type)
                                    @if ($loan->reminders->has($type))
                                        <span
                                            class="inline-block text-xs px-2 py-1 border rounded border-gray-700">{{ $type }}</span>
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-2">No upcoming repayments.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Missed Repayments --}}
    <div>
        <h2 class="text-xl font-semibold mb-2">Missed Repayments</h2>
        <div class="overflow-auto border border-gray-300 rounded">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-700 text-white">
                    <tr>
                        <th class="text-left px-4 py-2">Customer</th>
                        <th class="text-left px-4 py-2">Due Date</th>
                        <th class="text-left px-4 py-2">Amount Due</th>
                        <th class="text-left px-4 py-2">Days Overdue</th>
                        <th class="text-left px-4 py-2">Reminder Sent</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($missedRepayments as $loan)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $loan->user->first_name }}</td>
                            <td class="px-4 py-2">{{ $loan->due_date->toDateString() }}</td>
                            <td class="px-4 py-2">{{ number_format($loan->amount_due, 2) }}</td>
                            <td class="px-4 py-2">{{ $loan->days_overdue }}</td>
                            <td class="px-4 py-2">
                                @if ($loan->reminder_after)
                                    <span
                                        class="inline-block text-xs px-2 py-1 border rounded border-gray-700">after</span>
                                @else
                                    <span class="text-xs text-gray-500">Not sent</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-2">No missed repayments.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Reminder Logs --}}
    {{-- Reminder Logs --}}
<div>
    <div class="flex justify-between items-center mb-2">
        <h2 class="text-xl font-semibold">Reminder Logs</h2>
        <button wire:click="exportReminderLogs"
            class="text-sm px-4 py-1 border border-gray-700 rounded bg-white hover:bg-gray-100">
            Export CSV
        </button>
    </div>

    <div class="overflow-auto border border-gray-300 rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-700 text-white">
                <tr>
                    <th class="text-left px-4 py-2">Customer</th>
                    <th class="text-left px-4 py-2">Loan ID</th>
                    <th class="text-left px-4 py-2">Type</th>
                    <th class="text-left px-4 py-2">Sent At</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reminderLogs as $log)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $log->loan->customer->name ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $log->loan_id }}</td>
                        <td class="px-4 py-2">{{ ucfirst($log->type) }}</td>
                        <td class="px-4 py-2">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-2">No reminder logs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- <div class="mt-4">
        {{ $reminderLogs->links() }}
    </div> --}}
</div>


</div>

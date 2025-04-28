<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ScheduledReminder;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;


class ScheduledReminderManager extends Component
{
    use WithPagination;
    public $search = '';
    public $editingId = null;
    public $editMessage;
    public $editScheduledAt;

    protected $rules = [
        'editMessage' => 'nullable|string|max:255',
        'editScheduledAt' => 'required|date',
    ];

    public function mount()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        $this->editingId = $id;
        $reminder = ScheduledReminder::findOrFail($id);
        $this->editMessage = $reminder->message;
        $this->editScheduledAt = Carbon::parse($reminder->scheduled_at)->format('Y-m-d\TH:i');
    }

    public function updateReminder()
    {
        $this->validate();
        $reminder = ScheduledReminder::findOrFail($this->editingId);
        $reminder->update([
            'message' => $this->editMessage,
            'scheduled_at' => Carbon::parse($this->editScheduledAt),
        ]);

        $this->reset(['editingId', 'editMessage', 'editScheduledAt']);
        session()->flash('success', 'Reminder updated successfully.');
    }

    public function render()
    {
        $reminders = ScheduledReminder::with('user', 'loan')
            ->whereHas('user', function ($query) {
                $query->where('first_name', 'like', '%' . $this->search . '%');
            })
            ->orWhere('status', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.scheduled-reminder-manager', [
            'reminders' => $reminders
        ]);
    }
}



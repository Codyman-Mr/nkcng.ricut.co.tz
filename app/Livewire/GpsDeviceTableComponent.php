<?php

namespace App\Livewire;

use App\Models\GpsDevice;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class GpsDeviceTableComponent extends Component
{
    use WithPagination;

    public $search = '';
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = GpsDevice::query();

        if ($this->search) {
            $searchTerm = '%' . Str::lower($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(device_id) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(activity_status) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(assignment_status) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(power_status) LIKE ?', [$searchTerm]);
            });
        }

        $devices = $query->latest()->paginate(10);
        return view('livewire.gps-device-table-component',[
            'devices' => $devices,
        ]);
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class VehicleGps extends Component
{
    public $vehicles;

    protected $listeners = ['vehicleGpsUpdated' => 'refreshData'];

    public function mount()
    {
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->vehicles = Auth::user()->vehicles()->with('gpsRecords')->get();
    }

    public function render()
    {
        return view('livewire.vehicle-gps');
    }
}
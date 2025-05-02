<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GpsDevice;
use App\Models\Location;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserLocationTracker extends Component
{
    public $userId;
    public $deviceId;
    public $locations = [];

    public $user;

    protected $listeners = [
        'locationsUpdated' => 'handleLocationsUpdated',
    ];

    public function mount($userId)
    {
        $this->userId = $userId;
        $device = GpsDevice::where('user_id', $userId)->first();
        if ($device) {
            $this->deviceId = $device->device_id;
            $latest = Location::where('device_id', $this->deviceId)->latest()->first();
            if ($latest) {
                $this->locations[$this->deviceId] = [
                    'latitude' => (float) $latest->latitude,
                    'longitude' => (float) $latest->longitude,
                    'timestamp' => $latest->timestamp,
                ];
            } else {
                $this->locations[$this->deviceId] = [
                    'latitude' => 0,
                    'longitude' => 0,
                    'timestamp' => now()->toDateTimeString(),
                ];
            }
        }
        Log::info('Mounted UserLocationTracker for user: ' . $userId . ', device: ' . ($this->deviceId ?? 'none'));
    }

    public function handleLocationsUpdated($locations)
    {
        Log::info('Received locationsUpdated event: ' . json_encode($locations));
        if (isset($locations[$this->deviceId])) {
            $location = $locations[$this->deviceId];
            $this->locations[$this->deviceId] = [
                'latitude' => (float) $location['latitude'],
                'longitude' => (float) $location['longitude'],
                'timestamp' => $location['timestamp'],
            ];
            Log::info('Updated locations for device: ' . $this->deviceId . ': ' . json_encode($this->locations));
        }
    }

    public function render()
    {
        $this->user = Auth::user();
        return view('livewire.user-location-tracker', compact('user'));
    }
}
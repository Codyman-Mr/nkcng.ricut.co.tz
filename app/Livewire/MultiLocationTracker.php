<?php

/// app/Http/Livewire/MultiLocationTracker.php
namespace App\Livewire;


use Livewire\Component;
use App\Models\Location;
use Illuminate\Support\Facades\Log;

class MultiLocationTracker extends Component
{
    public $devices = [];
    public $locations = [];

    protected $listeners = [
        'locationsUpdated' => 'handleLocationsUpdated',
    ];

    public function mount($devices)
    {
        $this->devices = $devices;
        foreach ($this->devices as $deviceId) {
            $latest = Location::where('device_id', $deviceId)->latest()->first();
            if ($latest) {
                $this->locations[$deviceId] = [
                    'latitude' => (float) $latest->latitude,
                    'longitude' => (float) $latest->longitude,
                    'timestamp' => $latest->timestamp,
                ];
            } else {
                $this->locations[$deviceId] = [
                    'latitude' => (float) match ($deviceId) {
                        'device1' => 37.7749,
                        'device2' => 34.0522,
                        'device3' => 40.7128,
                        default => 0,
                    },
                    'longitude' => (float) match ($deviceId) {
                        'device1' => -122.4194,
                        'device2' => -118.2437,
                        'device3' => -74.0060,
                        default => 0,
                    },
                    'timestamp' => now()->toDateTimeString(),
                ];
            }
        }
        Log::info('Mounted with locations: ' . json_encode($this->locations));
    }

    public function handleLocationsUpdated($locations)
    {
        Log::info('Received locationsUpdated event: ' . json_encode($locations));
        $updated = false;
        foreach ($locations as $deviceId => $location) {
            if (in_array($deviceId, $this->devices)) {
                $newLat = (float) $location['latitude'];
                $newLon = (float) $location['longitude'];
                $newTime = $location['timestamp'];
                $current = $this->locations[$deviceId] ?? [];
                if (
                    !isset($current['latitude']) ||
                    $current['latitude'] !== $newLat ||
                    $current['longitude'] !== $newLon ||
                    $current['timestamp'] !== $newTime
                ) {
                    $this->locations[$deviceId] = [
                        'latitude' => $newLat,
                        'longitude' => $newLon,
                        'timestamp' => $newTime,
                    ];
                    $updated = true;
                }
            }
        }
        if ($updated) {
            Log::info('Updated locations: ' . json_encode($this->locations));
            
        }
    }

    public function render()
    {
        return view('livewire.multi-location-tracker', ['locations' => $this->locations]);
    }
}
<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Location;
use Illuminate\Support\Facades\Log;

class LocationTracker extends Component
{
    public $deviceId; // Single device_id or array for compatibility
    public $locations = [];

    protected $listeners = [
        'locationsUpdated' => 'handleLocationsUpdated',
    ];

    public function mount($deviceId)
    {
        $this->deviceId = $deviceId;

        if (!$deviceId) {
            Log::warning('No deviceId provided to LocationTracker');
            return;
        }

        $this->initializeLocations();
    }

    protected function initializeLocations()
    {
        $deviceIds = is_array($this->deviceId) ? $this->deviceId : [$this->deviceId];
        foreach ($deviceIds as $id) {
            $latest = Location::where('device_id', $id)->latest()->first();
            if ($latest) {
                $this->locations[$id] = [
                    'latitude' => (float) $latest->latitude,
                    'longitude' => (float) $latest->longitude,
                    'timestamp' => $latest->timestamp,
                ];
            } else {
                $this->locations[$id] = [
                    'latitude' => (float) match ($id) {
                        'device1' => 37.7749,
                        default => 0,
                    },
                    'longitude' => (float) match ($id) {
                        'device1' => -122.4194,
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
        $deviceIds = is_array($this->deviceId) ? $this->deviceId : [$this->deviceId];
        foreach ($locations as $deviceId => $location) {
            if (in_array($deviceId, $deviceIds)) {
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
        return view('livewire.location-tracker', ['locations' => $this->locations]);
    }
}

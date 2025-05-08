<?php

// app/Events/LocationUpdated.php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LocationUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $deviceId;
    public $latitude;
    public $longitude;
    public $timestamp;

    public function __construct($deviceId, $latitude, $longitude, $timestamp)
    {
        Log::info('Constructing LocationUpdated event for device: ' . $deviceId);
        if (is_array($deviceId)) {
            $this->deviceId = $deviceId['deviceId'] ?? $deviceId['device_id'];
            $this->latitude = $deviceId['latitude'];
            $this->longitude = $deviceId['longitude'];
            $this->timestamp = $deviceId['timestamp'];
        } else {
            $this->deviceId = $deviceId;
            $this->latitude = $latitude;
            $this->longitude = $longitude;
            $this->timestamp = $timestamp;
        }
    }

    public function broadcastOn()
    {
        Log::info('Broadcasting LocationUpdated on channel: locations');
        return new Channel('locations');
    }

    public function broadcastAs()
    {
        return 'LocationUpdated';
    }

    public function broadcastWith()
    {
        return [
            'deviceId' => $this->deviceId,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'timestamp' => $this->timestamp,
        ];
    }
}

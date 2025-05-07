<?php

// app/Http/Controllers/LocationController.php
namespace App\Http\Controllers;

use App\Events\LocationUpdated;
use App\Models\Location;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use APp\Models\GpsDevice;

class LocationController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|exists:gps_devices,device_id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'timestamp' => 'required|date',
        ]);

        $mysqlTimestamp = Carbon::parse($validated['timestamp'])->format('Y-m-d H:i:s');

        // Find the location entry for this device
        $location = Location::where('device_id', $validated['device_id'])->first();

        if ($location) {
            $location->update([
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'timestamp' => $mysqlTimestamp,
            ]);
        } else {
            // Optionally, create a new record if one doesn't exist
            Location::create([
                'device_id' => $validated['device_id'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'timestamp' => $mysqlTimestamp,
            ]);
        }

        Log::info('Firing LocationUpdated event for device: ' . $validated['device_id']);
        event(new LocationUpdated(
            $validated['device_id'],
            $validated['latitude'],
            $validated['longitude'],
            $validated['timestamp']
        ));

        return response()->json(['status' => 'success']);
    }

}






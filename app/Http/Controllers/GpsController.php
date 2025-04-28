<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GpsDevice;
use App\Events\LocationUpdated;

class GpsController extends Controller
{
    // GpsController.php

    public function store(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric',
        ]);

        $device = GpsDevice::firstOrCreate([
            'device_id' => $request->device_id,
        ]);

        $device->positions()->create([
            'latitude' => $request->lat,
            'longitude' => $request->lon,
            'speed' => $request->speed,
            'heading' => $request->heading,
            'recorded_at' => now(),
        ]);

        LocationUpdated::dispatch($device->id, $device->lat, $device->lng);


        return response()->json(['status' => 'success']);
    }

}

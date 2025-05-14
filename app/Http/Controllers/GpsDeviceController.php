<?php

namespace App\Http\Controllers;

use App\Models\GpsDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GpsDeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $devices = GpsDevice::all();
        $assignedDevices = GpsDevice::where('assignment_status', 'assigned')->get();
        $unassignedDevices = GpsDevice::where('assignment_status', 'unassigned')->get();
        $activeDevices = GpsDevice::where('activity_status', 'active')->get();
        $inactiveDevices = GpsDevice::where('activity_status', 'inactive')->get();
        $powerOnDevices = GpsDevice::where('power_status', 'ON')->get();
        $powerOffDevices = GpsDevice::where('power_status', 'OFF')->get();
        $deviceCount = [
            'total' => $devices->count(),
            'assigned' => $assignedDevices->count(),
            'unassigned' => $unassignedDevices->count(),
            'active' => $activeDevices->count(),
            'inactive' => $inactiveDevices->count(),
            'power_on' => $powerOnDevices->count(),
            'power_off' => $powerOffDevices->count(),
        ];
        $deviceStatus = [
            'assigned' => $assignedDevices,
            'unassigned' => $unassignedDevices,
            'active' => $activeDevices,
            'inactive' => $inactiveDevices,
            'power_on' => $powerOnDevices,
            'power_off' => $powerOffDevices,
        ];


        return view("gps-devices.gps-devices", [
            "devices"=> $devices,
            'deviceCount' => $deviceCount,
            'deviceStatus' => $deviceStatus,
        ]);
    }

    public function assignGpsDevice()
    {
        $devices = GpsDevice::all();
        return view("gps-devices.assign-gps-device", [
            'devices' => $devices,
        ]);
    }

    public function updatePowerStatus(Request $request)
    {
        $device = GpsDevice::where('device_id', $request->device_id)->firstOrFail();

        $request->validate([
            'power_status' => 'required|in:ON,OFF',
            'power_status_notes' => 'nullable|string|max:255',
        ]);

        $device->update([
            'power_status' => $request->power_status,
            'power_status_updated_at' => now(),
            'power_status_notes' => $request->power_status_notes,
        ]);

        // Simulate sending command to GPS device (e.g., via IoT platform or device API)
        try {
            $this->sendPowerCommand($device->device_id, $request->power_status);
        } catch (\Exception $e) {
            Log::error('Failed to send power command to device ' . $device->device_id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send command to device'], 500);
        }

        return response()->json([
            'message' => 'Power status updated successfully',
            'device' => $device,
        ]);

    }

    public function getPowerStatus(string $device_id)
    {
        $device = GpsDevice::where('device_id', $device_id)->firstOrFail();

        return response()->json([
            'device_id' => $device->device_id,
            'power_status' => $device->power_status,
            'power_status_updated_at' => $device->power_status_updated_at,
            'power_status_notes' => $device->power_status_notes,
        ]);
    }

    protected function sendPowerCommand(string $device_id, string $status)
    {
        // Placeholder for actual device communication
        // This would integrate with your GPS device's API or IoT platform
        // Example: Send HTTP request to device or IoT service
        // throw new \Exception('Device communication not implemented');
        // For demo, assume success
        Log::info("Sent $status command to device $device_id");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(GpsDevice $gpsDevice)
    {
        return view('gps-devices.show-gps-device', [
            'device' => $gpsDevice,
            'device_id' => $gpsDevice->device_id,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GpsDevice $gpsDevice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GpsDevice $gpsDevice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GpsDevice $gpsDevice)
    {
        //
    }
}

<?php

namespace App\Services;

use App\Models\CustomerVehicleGps;
use App\Models\CustomerVehicle;
use PhpMqtt\Client\MqttClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MqttService
{
    public function subscribe()
    {
        $client = new MqttClient('localhost', 1883);
        $client->connect(null, true);

        $client->subscribe('basic_data_topic', function ($topic, $message) {
            try {
                $this->processMessage($message);
            } catch (\Throwable $e) {
                Log::error('MQTT Processing Error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });

        $client->loop(true);
    }

   private function processMessage($message)
{
    try {
        $data = json_decode($message, true);

        // Validate JSON structure
        $validator = Validator::make($data, [
            'imei' => 'required|string|size:15',
            'data' => 'required|array|min:1',
            'data.0.lat' => 'required|numeric',
            'data.0.lng' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            Log::error('Invalid MQTT data', $validator->errors()->toArray());
            return;
        }

        // Find vehicle by IMEI (now exists in customer_vehicles)
        $vehicle = CustomerVehicle::where('imei', $data['imei'])->first();

        if (!$vehicle) {
            Log::error('Vehicle not found for IMEI: ' . $data['imei']);
            return;
        }

        // Save GPS data
        CustomerVehicleGps::updateOrCreate(
            ['imei' => $data['imei']],
            [
                'user_id' => $vehicle->user_id,
                'customer_vehicle_id' => $vehicle->id,
                'lat' => $data['data'][0]['lat'],
                'lng' => $data['data'][0]['lng'],
                'speed' => $data['data'][0]['speed'] ?? null,
                'timestamp' => now()
            ]
        );

        Log::info('GPS data saved successfully', ['imei' => $data['imei']]);

    } catch (\Throwable $e) {
        Log::error('Processing failed: ' . $e->getMessage());
    }
}
}

<?php

// app/Console/Commands/SubscribeMqtt.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MqttService;

class SubscribeMqtt extends Command
{
    protected $signature = 'mqtt:subscribe';
    protected $description = 'Subscribe to MQTT topic for GPS data';

    public function handle()
    {
        $mqttService = new MqttService();

        $mqttService->subscribe('gps/topic', function ($topic, $message) {
            // Process the GPS data
            $data = json_decode($message, true);

            // Broadcast the data to the frontend (see step 3)
            event(new \App\Events\GpsDataReceived($data));
        });
    }
}

<?php

use PhpMqtt\Client\MqttClient;
use App\Models\CustomerVehicleGps;

class MqttService
{
    public function subscribe()
    {
        $client = new MqttClient('localhost', 1883);
        $client->connect(null, true);

        $client->subscribe('basic_data_topic', function ($topic, $message) {
            $this->processMessage($message);
        });

        $client->loop(true);
    }

    protected function processMessage($message)
    {
        $data = json_decode($message, true);

        CustomerVehicleGps::updateOrCreate(
            ['imei' => $data['imei']],
            [
                'lat' => $data['data'][0]['lat'],
                'lng' => $data['data'][0]['lng'],
                'speed' => $data['data'][0]['speed'],
                // Map other fields
            ]
        );
    }
}

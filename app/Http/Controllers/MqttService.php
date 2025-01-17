<?php

// app/Services/MqttService.php
namespace App\Services;

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class MqttService{
    protected $mqtt;

    public function __construct()
    {
        $server = 'your-mqtt-broker-url'; // e.g., 'broker.hivemq.com'
        $port = 1883; // Default MQTT port
        $clientId = 'laravel-mqtt-client';
        $username = 'your-username'; // If authentication is required
        $password = 'your-password'; // If authentication is required

        $this->mqtt = new MqttClient($server, $port, $clientId);

        $connectionSettings = (new ConnectionSettings)
            ->setUsername($username)
            ->setPassword($password);

        $this->mqtt->connect($connectionSettings, true);
    }

    public function subscribe($topic, callable $callback)
    {
        $this->mqtt->subscribe($topic, $callback, 0);
        $this->mqtt->loop(true); // Keep the connection alive
    }

    public function disconnect()
    {
        $this->mqtt->disconnect();
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MqttService;

class RunMqttListener extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'mqtt listener for gps data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mqttService = new MqttService();
        $mqttService->subscribe();
    }
}

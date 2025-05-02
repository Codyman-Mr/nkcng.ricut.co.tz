<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GpsDeviceFactory extends Factory
{
    protected $model = \App\Models\GpsDevice::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'device_id' => function () {
                return \App\Models\Location::factory()->create()->id; // Create Location first
            },
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
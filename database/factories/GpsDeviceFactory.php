<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\CustomerVehicle;

class GpsDeviceFactory extends Factory
{
    protected $model = \App\Models\GpsDevice::class;

    public function definition()
    {
        return [
            'device_id' => $this->faker->unique()->randomNumber(),
            'activity_status' => 'inactive',
            'assignment_status' => 'unassigned',
            'power_status' => 'off',
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\CustomerVehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class CustomerVehicleFactory extends Factory
{
   protected $model = \App\Models\CustomerVehicle::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'imei' => $this->faker->numerify('###########'),
            'model' => $this->faker->word(),
            'plate_number' => $this->faker->regexify('T-\d{3}-[A-Z0-9]{3}'),
            'vehicle_type' => $this->faker->randomElement(['bajaj', 'car']),
            'fuel_type' => $this->faker->randomElement(['petrol', 'diesel']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

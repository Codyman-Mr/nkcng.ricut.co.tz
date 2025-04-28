<?php

namespace Database\Factories;

use App\Models\CustomerVehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class CustomerVehicleFactory extends Factory
{
    protected $model = CustomerVehicle::class;

    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'model' => $this->faker->word(),
            'plate_number' => strtoupper($this->faker->bothify('???-####')),
            'vehicle_type' => $this->faker->randomElement(['bajaj', 'car']),
            'fuel_type' => $this->faker->randomElement(['petrol', 'diesel']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

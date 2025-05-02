<?php

namespace Database\Factories;

use App\Models\Installation;
use App\Models\CustomerVehicle;
use App\Models\CylinderType;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstallationFactory extends Factory
{
    protected $model = Installation::class;

    public function definition()
    {
        return [
            'customer_vehicle_id' => CustomerVehicle::factory(), // Create or use an existing CustomerVehicle
            'cylinder_type_id' => CylinderType::factory(), // Create or use an existing
            'status' => $this->faker->randomElement(['pending', 'completed']), // Random status
            'payment_type' => $this->faker->randomElement(['direct', 'loan']), // Random status
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

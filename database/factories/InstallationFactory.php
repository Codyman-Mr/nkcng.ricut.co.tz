<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CustomerVehicle;
use App\Models\CylinderType;

class InstallationFactory extends Factory
{
    protected $model = \App\Models\Installation::class;

    public function definition()
    {
        return [
            'customer_vehicle_id' => CustomerVehicle::factory(),
            'cylinder_type_id' => $this->faker->randomElement([1, 2, 3]), // Match seeded CylinderType IDs
            'status' => $this->faker->randomElement(['pending', 'completed']),
            'payment_type' => 'loan',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

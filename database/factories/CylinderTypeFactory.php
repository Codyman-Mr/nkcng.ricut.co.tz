<?php

namespace Database\Factories;

use App\Models\CylinderType;
use Illuminate\Database\Eloquent\Factories\Factory;

class CylinderTypeFactory extends Factory
{
    protected $model = CylinderType::class;

    public function definition()
    {
        return [
            'id' => $this->faker->unique()->numberBetween(1,100),
            'name' => $this->faker->randomElement(['7L', '11L', '15L']),
        ];
    }
}

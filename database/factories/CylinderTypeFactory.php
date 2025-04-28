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
            'id' => $this->faker->unique()->numberBetween(50000, 99999),
            'name' => $this->faker->word(),
        ];
    }
}

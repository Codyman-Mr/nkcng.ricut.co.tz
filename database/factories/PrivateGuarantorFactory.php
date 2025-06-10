<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Loan;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PrivateGuarantor>
 */
class PrivateGuarantorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'loan_id' => Loan::factory(),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'phone_number' => '+255' . $this->faker->numberBetween(600000000, 799999999),
            'nida_no' => $this->faker->numerify('####################'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

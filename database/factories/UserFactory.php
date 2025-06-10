<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'phone_number' => fake()->numerify('07########'),
            'gender' => fake()->randomElement(['male', 'female']),
            'dob' => fake()->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
            'nida_number' => fake()->numerify('####################'),
            'address' => fake()->address(),

            'password' => '$2y$12$gNuGvZyWG4/NoqHsv2gWt.gZZBMc3evrwSKSNeynwIh9YAzCiEtoO',
            'verification_code' => fake()->numberBetween(100000, 999999),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

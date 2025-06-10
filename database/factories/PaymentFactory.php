<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Loan;
use App\Models\User;

class PaymentFactory extends Factory
{
    protected $model = \App\Models\Payment::class;

    public function definition()
    {
        return [
            'loan_id' => Loan::factory(),
            'users_id' => User::factory(),
            'payment_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'paid_amount' => $this->faker->numberBetween(50000, 200000),
            'payment_method' => $this->faker->randomElement(['bank_transfer', 'mobile_money', 'cash']),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'externa_id' => $this->faker->uuid,
            'provider' => $this->faker->randomElement(['Mpesa', 'Airtel Money', 'Tigo Pesa', 'Halopesa']),
            'payment_description' => $this->faker->sentence,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

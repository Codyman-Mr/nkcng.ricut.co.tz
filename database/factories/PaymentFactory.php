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
            'payment_description' => $this->faker->sentence,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

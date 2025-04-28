<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Select a random loan
        $loan = Loan::inRandomOrder()->first();

        return [
            'loan_id' => $loan->id,
            'paid_amount' => $this->faker->randomFloat(2, 0, $loan->loan_required_amount), // Random below loan_required_amount
            'payment_date' => $this->faker->dateTimeBetween($loan->loan_start_date, $loan->loan_end_date),
            'payment_method' => $this->faker->randomElement(['cash', 'bank_transfer', 'mobile_money']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

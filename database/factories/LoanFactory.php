<?php
namespace Database\Factories;

use App\Models\Loan;
use App\Models\User;
use App\Models\Installation;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(), // Create a user or use an existing one
            'installation_id' => Installation::factory(), // Create an installation or use an existing one
            'loan_type' => $this->faker->randomElement(['NK CNG Automotive Loan', 'Maendeleo Bank Loan']),
            'loan_required_amount' => $this->faker->numberBetween(1000000, 1000000),
            'loan_payment_plan' => $this->faker->randomElement(['weekly', 'bi-weekly', 'monthly']),
            'loan_start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'loan_end_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'status' => $this->faker->randomElement(['approved', 'pending', 'rejected']),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Installation;

class LoanFactory extends Factory
{
    protected $model = \App\Models\Loan::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'installation_id' => Installation::factory()->state(function () {
                return ['cylinder_type_id' => $this->faker->randomElement([1, 2, 3])];
            }),
            'loan_type' => $this->faker->randomElement(['NK CNG Automotive Loan', 'Maendeleo Bank Loan']),
            'loan_required_amount' => function (array $attributes) {
                $installation = Installation::find($attributes['installation_id']);
                return $installation && $installation->cylinder_type
                    ? \App\Models\LoanPackage::find($installation->cylinder_type->loan_package_id)->amount_to_finance ?? 1400000
                    : 1400000;
            },
            'loan_payment_plan' => function (array $attributes) {
                $installation = Installation::find($attributes['installation_id']);
                return $installation && $installation->cylinder_type
                    ? \App\Models\LoanPackage::find($installation->cylinder_type->loan_package_id)->payment_plan ?? 'weekly'
                    : 'weekly';
            },
            'loan_start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'loan_end_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'status' => $this->faker->randomElement(['approved', 'pending', 'rejected']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

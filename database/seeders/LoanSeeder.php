<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Loan;
use \App\Models\Payment;

class LoanSeeder extends Seeder
{
    public function run()
    {
        // Generate 50 loans with related users and installations
        Loan::factory()->count(100)->create();

       
    }
}

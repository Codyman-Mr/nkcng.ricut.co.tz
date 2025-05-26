<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LoanPackage;
use Illuminate\Support\Facades\DB;

class LoanPackageSeeder extends Seeder
{

    public function run(): void
    {


        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('loan_packages')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $packages = [
            [
                'id' => 1,
                'name' => 'Bajaji Package',
                'description' => '7L cylinder for Bajaji vehicles',
                'total_installation' => 1600000,
                'down_payment' => 200000,
                'amount_to_finance' => 1400000,
                'min_price' => 1400000,
                'max_price' => 1600000,
                'payment_plan' => 'weekly',
            ],

            [
                'id' => 2,
                'name' => 'Bajaji Package',
                'description' => '7L cylinder for Bajaji vehicles',
                'total_installation' => 1600000,
                'down_payment' => 200000,
                'amount_to_finance' => 1400000,
                'min_price' => 1400000,
                'max_price' => 1600000,
                'payment_plan' => 'weekly',

            ],
            [
                'id' => 3,
                'name' => 'Bajaji Package',
                'description' => '7L cylinder for Bajaji vehicles',
                'total_installation' => 1600000,
                'down_payment' => 200000,
                'amount_to_finance' => 1400000,
                'min_price' => 1400000,
                'max_price' => 1600000,
                'payment_plan' => 'weekly',
            ]
        ];



        foreach ($packages as $package) {
            LoanPackage::create($package);
        }
    }
}

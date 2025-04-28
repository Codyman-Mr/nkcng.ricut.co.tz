<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CylinderType;
use Illuminate\Database\Seeder;
use App\Models\CustomerVehicle;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {


        // Seed users
        User::factory()->count(10)->create();

        // Seed customer vehicles
        CustomerVehicle::factory()->count(20)->create();

        $this->call(LoanSeeder::class);



    }
}

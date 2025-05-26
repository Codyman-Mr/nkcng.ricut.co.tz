<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CylinderType;
use Illuminate\Support\Facades\DB;

class CylinderTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('cylinder_types')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $packageIds = \App\Models\LoanPackage::pluck('id')->toArray();

        if (count($packageIds) < 3) {
            throw new \Exception('Not enough LoanPackage records found. Please seed LoanPackage first.');
        }

        CylinderType::create([
            'id' => 1,
            'name' => '7L Cylinder',
            'capacity' => 7,
            'loan_package_id' => $packageIds[0]
        ]);

        CylinderType::create([
            'id' => 2,
            'name' => '11L Cylinder',
            'capacity' => 11,
            'loan_package_id' => $packageIds[1]
        ]);

        CylinderType::create([
            'id' => 3,
            'name' => '15L Cylinder',
            'capacity' => 15,
            'loan_package_id' => $packageIds[2]
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\GpsDevice;
use App\Models\Location;
use App\Models\CustomerVehicle;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\Installation;
use App\Models\CylinderType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\LoanPackage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data and disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        CylinderType::query()->delete();
        LoanPackage::query()->delete();
        DB::table('cylinder_types')->truncate();
        DB::table('loan_packages')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Log::info('Starting seeding');
        // Seed Loan Packages and Cylinder Types
        $this->call([
            LoanPackageSeeder::class,
            CylinderTypeSeeder::class,
        ]);

        if (CylinderType::count() !== 3) {
            throw new \Exception('Cylinder types not seeded properly');
        }

        // Seed 60 users with loans (30 with payments, 30 without)
        User::factory()
            ->count(60)
            ->has(
                GpsDevice::factory()
                    ->afterCreating(function (GpsDevice $device) {
                        $location = $device->location;
                        if ($location) {
                            $location->update(['device_id' => $device->id]);
                        }
                    })
                    ->has(Location::factory(), 'location')
            )
            ->afterCreating(function (User $user) {
                // Create CustomerVehicle for the user
                $vehicle = CustomerVehicle::factory()->create([
                    'user_id' => $user->id,
                ]);

                // Create Installation for the vehicle
                $installation = Installation::factory()->create([
                    'customer_vehicle_id' => $vehicle->id,
                    'cylinder_type_id' => CylinderType::with('loanPackage')->inRandomOrder()->first()->id,
                    'status' => 'pending',
                    'payment_type' => 'loan',
                ]);

                // Log for debugging
                Log::info('Installation ID: ' . $installation->id);
                Log::info('Cylinder Type ID: ' . $installation->cylinder_type_id);

                // Eager load relationships
                $cylinderType = $installation->cylinderType()->with('loanPackage')->first();
                if (!$cylinderType) {
                    Log::error("Missing cylinder type for installation", ['installation' => $installation->id]);
                    return;
                }

                if (!$cylinderType->loanPackage) {
                    Log::error("Cylinder type missing loan package", [
                        'cylinder_type' => $cylinderType->id,
                        'loan_package_id' => $cylinderType->loan_package_id
                    ]);
                    return;
                }

                $loanPackage = $cylinderType->loanPackage;

                $loan = Loan::factory()->create([
                    'installation_id' => $installation->id,
                    'user_id' => $user->id,
                    'loan_required_amount' => $loanPackage->amount_to_finance,
                    'loan_payment_plan' => $loanPackage->payment_plan,
                    'status' => 'pending',
                    'loan_type' => 'NK CNG Automotive Loan',
                    'loan_start_date' => now(),
                    'loan_end_date' => now()->addYear(),
                ]);

                // Create Payments for ~50% of loans
                if (rand(0, 1)) {
                    Payment::factory()->count(rand(1, 5))->create([
                        'loan_id' => $loan->id,
                        'users_id' => $user->id,
                    ]);
                }
            })
            ->create();

        // Seed 40 users without loans
        User::factory()
            ->count(40)
            ->create();

        Log::info('Seeding completed');
    }
}

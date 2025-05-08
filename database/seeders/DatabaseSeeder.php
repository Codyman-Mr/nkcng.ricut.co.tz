<?php

// namespace Database\Seeders;

// use App\Models\User;
// use App\Models\GpsDevice;
// use App\Models\Location;
// use App\Models\CustomerVehicle;
// use App\Models\Loan;
// use App\Models\Payment;
// use Illuminate\Database\Seeder;

// class DatabaseSeeder extends Seeder
// {
//     public function run(): void
//     {
//         // Seed 60 users with loans and full related data
//         User::factory()
//             ->count(60)
//             ->has(
//                 GpsDevice::factory()
//                     ->afterCreating(function (GpsDevice $device) {
//                         // Update the associated location’s device_id
//                         $location = $device->location;
//                         if ($location) {
//                             $location->update(['device_id' => $device->id]);
//                         }
//                     })
//                     ->has(Location::factory(), 'location')
//             )
//             ->has(CustomerVehicle::factory(), 'vehicles') // Use 'vehicles' relationship
//             ->has(
//                 Loan::factory()
//                     ->count(1)
//                     ->state(function (array $attributes, User $user) {
//                         return ['user_id' => $user->id];
//                     })
//                     ->when(rand(0, 1), function ($loan) {
//                         $loan->has(Payment::factory()->count(rand(1, 3)));
//                     })
//             )
//             ->create();

//         // Seed 40 users without loans
//         User::factory()
//             ->count(40)
//             ->has(
//                 GpsDevice::factory()
//                     ->afterCreating(function (GpsDevice $device) {
//                         // Update the associated location’s device_id
//                         $location = $device->location;
//                         if ($location) {
//                             $location->update(['device_id' => $device->id]);
//                         }
//                     })
//                     ->has(Location::factory(), 'location')
//             )
//             ->create();
//     }
// }


namespace Database\Seeders;

use App\Models\User;
use App\Models\GpsDevice;
use App\Models\Location;
use App\Models\CustomerVehicle;
use App\Models\Loan;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed 60 users with loans and full related data
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
            ->has(CustomerVehicle::factory(), 'vehicles')
            ->has(
                Loan::factory()
                    ->count(1)
                    ->state(function (array $attributes, User $user) {
                        return ['user_id' => $user->id];
                    })
                    ->when(rand(0, 1), function ($loan) {
                        $loan->has(Payment::factory()->count(rand(1, 3)));
                    })
            )
            ->create();

        // Seed 40 users without loans
        User::factory()
            ->count(40)

            ->create();
    }
}





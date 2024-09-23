<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CylinderType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'first_name' => 'Ebenezer',
            'last_name' => 'Ringo',
            'phone_number' => '0768591818',
            'gender' => 'male',
            'dob' => '2004-05-18',
            'password' => '$2y$12$YYFlEi10UfMQOMDWXGlKruQIQrSVogLrqncUvPZ7B5SS20BZuP6Hm',
            'verification_code' => 1111,
            'status' => 'verified',
            'role' => 'customer',
        ]);

        User::create([
            'first_name' => 'Ester',
            'last_name' => 'Msandi',
            'phone_number' => '0767975796',
            'gender' => 'female',
            'dob' => '1987-11-24',
            'password' => '$2y$12$LeJ6fkx0ON6DDBnM36DEDu0VyZsWbDU92YSCSvQmloUoXzMf.BRju',
            'verification_code' => 1111,
            'status' => 'verified',
            'role' => 'admin',
        ]);

        $cylinderTypes = [
            ['name' => '7Kg'],
            ['name' => '11Kg'],
            ['name' => '15Kg'],
            ['name' => '17Kg'],
        ];

        foreach ($cylinderTypes as $type) {
            CylinderType::create($type);
        }
    }
}
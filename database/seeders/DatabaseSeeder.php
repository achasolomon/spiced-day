<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Run individual seeders
        $this->call([
            RoleSeeder::class,
            DocumentCategorySeeder::class,
            DocumentTypeSeeder::class,
            InspectionChecklistsSeeder::class,
        ]);

        // Create Default Users (idempotent)
        $admin = User::firstOrCreate(
            ['email' => 'admin@spiceddayhome.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('admin123'),
                'user_type' => 'admin',
                'phone' => '555-0001',
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        $consultant = User::firstOrCreate(
            ['email' => 'consultant@spiceddayhome.com'],
            [
                'name' => 'Jane Smith',
                'password' => Hash::make('consultant123'),
                'user_type' => 'consultant',
                'phone' => '555-0002',
                'email_verified_at' => now(),
            ]
        );
        $consultant->assignRole('consultant');

        $applicant = User::firstOrCreate(
            ['email' => 'applicant@spiceddayhome.com'],
            [
                'name' => 'Mary Johnson',
                'password' => Hash::make('applicant123'),
                'user_type' => 'applicant',
                'phone' => '555-0003',
                'email_verified_at' => now(),
            ]
        );
        $applicant->assignRole('applicant');
    }
}
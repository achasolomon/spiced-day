<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InspectionChecklistsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            InitialInspectionChecklistSeeder::class,
            SecondAndFinalInspectionChecklistSeeder::class,
            ComplianceInspectionChecklistSeeder::class,
        ]);

        $this->command->info('All inspection checklists seeded successfully!');
    }
}
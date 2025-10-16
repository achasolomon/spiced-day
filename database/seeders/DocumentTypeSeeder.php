<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Required Document',
                'slug' => 'required-document',
                'description' => 'Mandatory documents required for application',
                'sort_order' => 1,
            ],
            [
                'name' => 'Supporting Document',
                'slug' => 'supporting-document',
                'description' => 'Additional supporting documentation',
                'sort_order' => 2,
            ],
            [
                'name' => 'Inspection Photo',
                'slug' => 'inspection-photo',
                'description' => 'Photos from facility inspections',
                'sort_order' => 3,
            ],
            [
                'name' => 'Certificate',
                'slug' => 'certificate',
                'description' => 'Official certificates and credentials',
                'sort_order' => 4,
            ],
            [
                'name' => 'Form',
                'slug' => 'form',
                'description' => 'Completed forms and applications',
                'sort_order' => 5,
            ],
            [
                'name' => 'Agreement',
                'slug' => 'agreement',
                'description' => 'Signed agreements and contracts',
                'sort_order' => 6,
            ],
            [
                'name' => 'Policy',
                'slug' => 'policy',
                'description' => 'Policy documents and procedures',
                'sort_order' => 7,
            ],
            [
                'name' => 'Reference',
                'slug' => 'reference',
                'description' => 'Reference letters and recommendations',
                'sort_order' => 8,
            ],
        ];

        foreach ($types as $type) {
            DB::table('document_types')->insert([
                'name' => $type['name'],
                'slug' => $type['slug'],
                'description' => $type['description'],
                'sort_order' => $type['sort_order'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
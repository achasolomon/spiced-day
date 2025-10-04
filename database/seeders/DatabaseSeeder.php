<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\DocumentRequirement;
use App\Models\InspectionChecklist;
use App\Models\InspectionItem;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $adminRole = Role::create(['name' => 'admin']);
        $consultantRole = Role::create(['name' => 'consultant']);
        $applicantRole = Role::create(['name' => 'applicant']);
        
        // Create Permissions
        $permissions = [
            'view applications',
            'create applications', 
            'update applications',
            'delete applications',
            'approve applications',
            'reject applications',
            'schedule inspections',
            'conduct inspections',
            'upload documents',
            'review documents',
            'manage users',
            'view reports',
            'export data',
            'system configuration'
        ];
        
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        
        // Assign Permissions to Roles
        $adminRole->givePermissionTo(Permission::all());
        $consultantRole->givePermissionTo([
            'view applications', 'update applications', 'approve applications', 
            'reject applications', 'schedule inspections', 'conduct inspections',
            'review documents', 'view reports'
        ]);
        $applicantRole->givePermissionTo([
            'create applications', 'update applications', 'upload documents'
        ]);
        
        // Create Default Users
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@spiceddayhome.com',
            'password' => Hash::make('admin123'),
            'user_type' => 'admin',
            'phone' => '555-0001',
            'email_verified_at' => now()
        ]);
        $admin->assignRole($adminRole);
        
        $consultant = User::create([
            'name' => 'Jane Smith',
            'email' => 'consultant@spiceddayhome.com', 
            'password' => Hash::make('consultant123'),
            'user_type' => 'consultant',
            'phone' => '555-0002',
            'email_verified_at' => now()
        ]);
        $consultant->assignRole($consultantRole);
        
        $applicant = User::create([
            'name' => 'Mary Johnson',
            'email' => 'applicant@spiceddayhome.com',
            'password' => Hash::make('applicant123'),
            'user_type' => 'applicant', 
            'phone' => '555-0003',
            'email_verified_at' => now()
        ]);
        $applicant->assignRole($applicantRole);
        
        // Seed Document Requirements
        $this->seedDocumentRequirements();
        
        // Seed Inspection Checklists
        $this->seedInspectionChecklists();
    }
    
    private function seedDocumentRequirements()
    {
        $requirements = [
            [
                'name' => 'Educator Certificate',
                'slug' => 'educator-certificate',
                'description' => 'Valid early childhood education certificate or equivalent qualification',
                'category' => 'educator_certificate',
                'stage' => 'document_submission',
                'is_required' => true,
                'accepted_formats' => ['pdf', 'jpg', 'png'],
                'has_expiry' => true,
                'validity_period' => 1095, // 3 years
                'sort_order' => 1
            ],
            [
                'name' => 'CPR/First Aid Certificate',
                'slug' => 'cpr-first-aid',
                'description' => 'Current CPR and First Aid certification',
                'category' => 'cpr_first_aid',
                'stage' => 'document_submission',
                'is_required' => true,
                'accepted_formats' => ['pdf', 'jpg', 'png'],
                'has_expiry' => true,
                'validity_period' => 365, // 1 year
                'sort_order' => 2
            ],
            [
                'name' => 'Criminal Record Check',
                'slug' => 'criminal-record-check', 
                'description' => 'Recent criminal background check including vulnerable sector check',
                'category' => 'criminal_record_check',
                'stage' => 'document_submission',
                'is_required' => true,
                'accepted_formats' => ['pdf'],
                'has_expiry' => true,
                'validity_period' => 180, // 6 months
                'sort_order' => 3
            ],
            [
                'name' => 'Home Insurance',
                'slug' => 'home-insurance',
                'description' => 'Proof of home insurance with minimum $1 million coverage',
                'category' => 'home_insurance',
                'stage' => 'document_submission', 
                'is_required' => true,
                'accepted_formats' => ['pdf'],
                'has_expiry' => true,
                'validity_period' => 365,
                'sort_order' => 4
            ],
            [
                'name' => 'Food Handler Certificate',
                'slug' => 'food-handler-certificate',
                'description' => 'Food safety certification for meal preparation',
                'category' => 'food_handler_certificate',
                'stage' => 'document_submission',
                'is_required' => true,
                'accepted_formats' => ['pdf', 'jpg', 'png'],
                'has_expiry' => true,
                'validity_period' => 1095, // 3 years
                'sort_order' => 5
            ]
        ];
        
        foreach ($requirements as $requirement) {
            DocumentRequirement::create($requirement);
        }
    }
    
    private function seedInspectionChecklists()
    {
        // Create Initial Inspection Checklist
        $initialChecklist = InspectionChecklist::create([
            'name' => 'Initial Home Inspection',
            'slug' => 'initial-home-inspection',
            'description' => 'Comprehensive safety and suitability assessment for new dayHome applications',
            'inspection_type' => 'initial_inspection',
            'dayhome_type' => 'all',
            'is_active' => true,
            'is_default' => true,
            'passing_score' => 80.00,
            'estimated_duration' => 120
        ]);
        
        // Create Inspection Items for Initial Checklist
        $initialItems = [
            [
                'code' => 'SF-001',
                'title' => 'Fire Extinguisher Present',
                'description' => 'Appropriate fire extinguisher is present and accessible',
                'category' => 'safety',
                'subcategory' => 'fire_safety',
                'response_type' => 'yes_no',
                'is_critical' => true,
                'weight' => 5,
                'section' => 'Fire Safety',
                'sort_order' => 1
            ],
            [
                'code' => 'SF-002', 
                'title' => 'Smoke Detectors',
                'description' => 'Working smoke detectors on each level of the home',
                'category' => 'safety',
                'subcategory' => 'fire_safety',
                'response_type' => 'yes_no',
                'is_critical' => true,
                'weight' => 5,
                'section' => 'Fire Safety',
                'sort_order' => 2
            ],
            [
                'code' => 'SF-003',
                'title' => 'Emergency Exit Plan',
                'description' => 'Clear emergency evacuation plan is posted and practiced',
                'category' => 'safety',
                'subcategory' => 'emergency_procedures',
                'response_type' => 'yes_no',
                'weight' => 3,
                'section' => 'Emergency Procedures',
                'sort_order' => 3
            ],
            [
                'code' => 'HE-001',
                'title' => 'General Cleanliness',
                'description' => 'Overall cleanliness and hygiene standards of the home',
                'category' => 'health',
                'subcategory' => 'hygiene',
                'response_type' => 'rating_scale',
                'response_options' => ['1' => 'Poor', '2' => 'Fair', '3' => 'Good', '4' => 'Very Good', '5' => 'Excellent'],
                'weight' => 3,
                'section' => 'Health & Hygiene',
                'sort_order' => 4
            ],
            [
                'code' => 'EN-001',
                'title' => 'Child-Safe Environment',
                'description' => 'Home environment is safe and appropriate for children',
                'category' => 'environment',
                'subcategory' => 'child_safety',
                'response_type' => 'yes_no',
                'is_critical' => true,
                'weight' => 5,
                'section' => 'Environment',
                'sort_order' => 5
            ]
        ];
        
        foreach ($initialItems as $item) {
            InspectionItem::create(array_merge($item, [
                'checklist_id' => $initialChecklist->id,
                'is_active' => true
            ]));
        }
        
        // Update total_items count
        $initialChecklist->update(['total_items' => count($initialItems)]);
        
        // Create Second Inspection Checklist
        $secondChecklist = InspectionChecklist::create([
            'name' => 'Second Home Inspection',
            'slug' => 'second-home-inspection', 
            'description' => 'Follow-up inspection focusing on documentation and compliance',
            'inspection_type' => 'second_inspection',
            'dayhome_type' => 'all',
            'is_active' => true,
            'is_default' => true,
            'passing_score' => 85.00,
            'estimated_duration' => 90
        ]);
    }
}
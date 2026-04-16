<?php

namespace Database\Seeders;

use App\Models\InspectionChecklist;
use App\Models\InspectionItem;
use Illuminate\Database\Seeder;

class SecondAndFinalInspectionChecklistSeeder extends Seeder
{
    public function run()
    {
        // The 60 shared items for second, final, and compliance inspections
        // ALL items are critical in these inspection types
        $sharedItems = [
            // Critical Safety Items (from initial inspection that continue)
            ['category' => 'safety', 'title' => 'Electrical Outlets Covered'],
            ['category' => 'safety', 'title' => 'Windows Have Guards (Openable by Adults)'],
            ['category' => 'safety', 'title' => 'Staircases Have Solid Gates & Latches'],
            ['category' => 'safety', 'title' => 'Chemicals, Alcohol, Cannabis Inaccessible'],
            ['category' => 'safety', 'title' => 'Cabinets & Medicine Drawers Latched'],
            ['category' => 'safety', 'title' => 'Cleaning Products Inaccessible'],
            ['category' => 'safety', 'title' => 'Sharp Objects Locked'],
            ['category' => 'safety', 'title' => 'No Flammables Near Stove'],
            ['category' => 'safety', 'title' => 'No Toxic Equipment Accessible'],
            ['category' => 'safety', 'title' => 'No Unsupervised Access to Garage/Shed'],
            ['category' => 'safety', 'title' => 'Hot Water <50°C'],
            ['category' => 'safety', 'title' => 'Medications Locked (Except EpiPen)'],
            ['category' => 'safety', 'title' => 'Fire Extinguisher Present & Known'],
            ['category' => 'safety', 'title' => 'Smoke & CO2 Detectors Working'],

            // Environment & Space
            ['category' => 'environment', 'title' => 'Play Area Fenced or Approved Park'],
            ['category' => 'environment', 'title' => 'Indoor Space Meets Size Requirements'],
            ['category' => 'environment', 'title' => 'Outdoor Play Area Properly Fenced'],
            ['category' => 'environment', 'title' => 'Sleeping Areas Meet Standards'],

            // Equipment
            ['category' => 'equipment', 'title' => 'Equipment Safe & Age-Appropriate'],
            ['category' => 'equipment', 'title' => 'Cribs/Cots Comply with Regulations'],
            ['category' => 'equipment', 'title' => 'Highchairs Meet CSA Standards'],
            ['category' => 'equipment', 'title' => 'All Toys Safe & In Good Repair'],
            ['category' => 'equipment', 'title' => 'Sufficient Variety & Quantity of Toys'],
            ['category' => 'equipment', 'title' => 'Car Seats Available (if applicable)'],

            // Health & Hygiene
            ['category' => 'health', 'title' => 'Surfaces Clean & Sanitized'],
            ['category' => 'health', 'title' => 'Fridge ≤4°C, Freezer ≤-18°C'],
            ['category' => 'health', 'title' => 'No Signs of Pests'],
            ['category' => 'health', 'title' => 'Bathroom Clean & Sanitized'],
            ['category' => 'health', 'title' => 'Kitchen Meets All Health Standards'],
            ['category' => 'health', 'title' => 'Food Storage Meets Requirements'],
            ['category' => 'health', 'title' => 'Refrigerator Temperature ≤4°C'],
            ['category' => 'health', 'title' => 'Freezer Temperature ≤-18°C'],
            ['category' => 'health', 'title' => 'Diaper Changing Area Sanitary'],
            ['category' => 'health', 'title' => 'Handwashing Facilities Accessible'],
            ['category' => 'health', 'title' => 'Proper Waste Disposal'],

            // Documentation
            ['category' => 'documentation', 'title' => 'Portable Records Binder'],
            ['category' => 'documentation', 'title' => 'Medication Forms & Allergy List'],
            ['category' => 'documentation', 'title' => 'Menu Posted'],
            ['category' => 'documentation', 'title' => 'Program Planning Posted'],
            ['category' => 'documentation', 'title' => 'Emergency Contacts Posted'],
            ['category' => 'documentation', 'title' => 'Allergies Posted'],
            ['category' => 'documentation', 'title' => 'All Required Postings Displayed'],
            ['category' => 'documentation', 'title' => 'Emergency Contact List Posted'],
            ['category' => 'documentation', 'title' => 'Allergy List Posted & Current'],
            ['category' => 'documentation', 'title' => 'Program Planning Available'],
            ['category' => 'documentation', 'title' => 'Weekly Menu Posted'],
            ['category' => 'documentation', 'title' => 'Policies & Procedures Manual Complete'],
            ['category' => 'documentation', 'title' => 'Child Records Complete'],
            ['category' => 'documentation', 'title' => 'Attendance Tracking System'],
            ['category' => 'documentation', 'title' => 'Parent Contracts Available'],

            // Emergency Procedures
            ['category' => 'emergency_procedures', 'title' => 'First Aid Kit Present'],
            ['category' => 'emergency_procedures', 'title' => 'Evacuation Plan & Emergency Numbers'],
            ['category' => 'emergency_procedures', 'title' => 'Emergency Plan Posted'],
            ['category' => 'emergency_procedures', 'title' => 'Fire Drill Log (Monthly)'],
            ['category' => 'emergency_procedures', 'title' => 'Evacuation Plan Posted & Practiced'],
            ['category' => 'emergency_procedures', 'title' => 'Emergency Procedures Understood'],
            ['category' => 'emergency_procedures', 'title' => 'Emergency Backpack Fully Prepared'],

            // Staff Qualifications
            ['category' => 'staff_qualifications', 'title' => 'Food Handler Certification'],
            ['category' => 'staff_qualifications', 'title' => 'First Aid & CPR Certified'],
            ['category' => 'staff_qualifications', 'title' => 'Criminal Record Check Completed'],
            ['category' => 'staff_qualifications', 'title' => 'Child Intervention Check Clear'],
            ['category' => 'staff_qualifications', 'title' => 'References Verified'],
            ['category' => 'staff_qualifications', 'title' => 'Education/Training Certificates'],
        ];

        // Create SECOND Inspection Checklist
        $secondChecklist = InspectionChecklist::updateOrCreate(
            ['slug' => 'second-inspection'],
            [
                'name' => 'SPICE\'d Second Inspection',
                'description' => 'Follow-up inspection to verify initial concerns have been addressed. All items are critical.',
                'inspection_type' => 'second_inspection',
                'dayhome_type' => 'all',
                'is_active' => true,
                'is_default' => true,
                'passing_score' => 100.00,
                'estimated_duration' => 90,
                'instructions' => 'ALL items must pass. Any failure requires consultant decision on next steps.',
            ]
        );

        // Create FINAL Inspection Checklist
        $finalChecklist = InspectionChecklist::updateOrCreate(
            ['slug' => 'final-inspection'],
            [
                'name' => 'SPICE\'d Final Inspection',
                'description' => 'Comprehensive final inspection before approval. All items are critical.',
                'inspection_type' => 'final_inspection',
                'dayhome_type' => 'all',
                'is_active' => true,
                'is_default' => true,
                'passing_score' => 100.00,
                'estimated_duration' => 120,
                'instructions' => 'Complete comprehensive review. ALL items must pass. Any failure requires consultant decision.',
            ]
        );

        // Create SCHEDULED Compliance Inspection Checklist
        $complianceScheduledChecklist = InspectionChecklist::updateOrCreate(
            ['slug' => 'compliance-inspection-scheduled'],
            [
                'name' => 'SPICE\'d Scheduled Compliance Inspection',
                'description' => 'Scheduled compliance check for active dayhomes. Educator is notified in advance.',
                'inspection_type' => 'compliance_inspection',
                'dayhome_type' => 'all',
                'is_active' => true,
                'is_default' => true,
                'passing_score' => 100.00,
                'estimated_duration' => 90,
                'instructions' => 'Scheduled compliance inspection. ALL items must pass to maintain active status.',
            ]
        );

        // Create UNSCHEDULED Compliance Inspection Checklist
        $complianceUnscheduledChecklist = InspectionChecklist::updateOrCreate(
            ['slug' => 'compliance-inspection-unscheduled'],
            [
                'name' => 'SPICE\'d Unscheduled Compliance Inspection',
                'description' => 'Unannounced compliance check for active dayhomes. No prior notification.',
                'inspection_type' => 'compliance_inspection',
                'dayhome_type' => 'all',
                'is_active' => true,
                'is_default' => true,
                'passing_score' => 100.00,
                'estimated_duration' => 90,
                'instructions' => 'Unscheduled compliance inspection. ALL items must pass to maintain active status.',
            ]
        );

        // Add items to SECOND inspection
        foreach ($sharedItems as $index => $item) {
            InspectionItem::updateOrCreate(
                ['checklist_id' => $secondChecklist->id, 'code' => 'SEC-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT)],
                [
                    'title' => $item['title'],
                    'category' => $item['category'],
                    'response_type' => 'yes_no_na',
                    'sort_order' => $index + 1,
                    'is_critical_second' => true, // ALL critical for second
                    'is_mandatory' => true,
                    'points_possible' => 5,
                    'requires_comment' => true,
                    'is_active' => true,
                    'included_in_initial' => false,
                    'included_in_second' => true,
                    'included_in_final' => true,
                    'included_in_compliance' => true,
                    'description' => 'Second inspection: ' . $item['title'],
                ]
            );
        }

        // Add items to FINAL inspection
        foreach ($sharedItems as $index => $item) {
            InspectionItem::updateOrCreate(
                ['checklist_id' => $finalChecklist->id, 'code' => 'FIN-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT)],
                [
                    'title' => $item['title'],
                    'category' => $item['category'],
                    'response_type' => 'yes_no_na',
                    'sort_order' => $index + 1,
                    'is_critical_final' => true, // ALL critical for final
                    'is_mandatory' => true,
                    'points_possible' => 5,
                    'requires_comment' => true,
                    'is_active' => true,
                    'included_in_initial' => false,
                    'included_in_second' => true,
                    'included_in_final' => true,
                    'included_in_compliance' => true,
                    'description' => 'Final inspection: ' . $item['title'],
                ]
            );
        }

        // Add items to SCHEDULED Compliance inspection
        foreach ($sharedItems as $index => $item) {
            InspectionItem::updateOrCreate(
                ['checklist_id' => $complianceScheduledChecklist->id, 'code' => 'COMP-SCH-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT)],
                [
                    'title' => $item['title'],
                    'category' => $item['category'],
                    'response_type' => 'yes_no_na',
                    'sort_order' => $index + 1,
                    'is_critical_compliance' => true, // ALL critical for compliance
                    'is_mandatory' => true,
                    'points_possible' => 5,
                    'requires_comment' => true,
                    'is_active' => true,
                    'included_in_initial' => false,
                    'included_in_second' => true,
                    'included_in_final' => true,
                    'included_in_compliance' => true,
                    'description' => 'Scheduled compliance inspection: ' . $item['title'],
                ]
            );
        }

        // Add items to UNSCHEDULED Compliance inspection
        foreach ($sharedItems as $index => $item) {
            InspectionItem::updateOrCreate(
                ['checklist_id' => $complianceUnscheduledChecklist->id, 'code' => 'COMP-UNSCH-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT)],
                [
                    'title' => $item['title'],
                    'category' => $item['category'],
                    'response_type' => 'yes_no_na',
                    'sort_order' => $index + 1,
                    'is_critical_compliance' => true, // ALL critical for compliance
                    'is_mandatory' => true,
                    'points_possible' => 5,
                    'requires_comment' => true,
                    'is_active' => true,
                    'included_in_initial' => false,
                    'included_in_second' => true,
                    'included_in_final' => true,
                    'included_in_compliance' => true,
                    'description' => 'Unscheduled compliance inspection: ' . $item['title'],
                ]
            );
        }

        $secondChecklist->updateTotalItems();
        $finalChecklist->updateTotalItems();
        $complianceScheduledChecklist->updateTotalItems();
        $complianceUnscheduledChecklist->updateTotalItems();

        $this->command->info('Second, Final, and Compliance inspection checklists created!');
        $this->command->info('All checklists have ' . count($sharedItems) . ' critical items');
    }
}
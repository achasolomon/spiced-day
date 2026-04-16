<?php

namespace Database\Seeders;

use App\Models\InspectionChecklist;
use App\Models\InspectionItem;
use Illuminate\Database\Seeder;

class ComplianceInspectionChecklistSeeder extends Seeder
{
    public function run()
    {
        $checklist = InspectionChecklist::updateOrCreate(
            ['slug' => 'compliance-inspection'],
            [
                'name' => 'SPICE\'d Compliance Inspection',
                'description' => 'Compliance inspection for active dayhome providers to ensure ongoing standards.',
                'inspection_type' => 'compliance_inspection',
                'dayhome_type' => 'all',
                'is_active' => true,
                'is_default' => true,
                'passing_score' => 100.00,
                'estimated_duration' => 60,
                'instructions' => 'Complete all items. Focus on ongoing compliance with regulations and quality standards.',
            ]
        );

        $items = [
            // Documentation Compliance
            ['category' => 'documentation', 'title' => 'Current License Displayed', 'response_type' => 'yes_no_na', 'is_critical' => true],
            ['category' => 'documentation', 'title' => 'Up-to-date Child Records', 'response_type' => 'yes_no_na', 'is_critical' => true],
            ['category' => 'documentation', 'title' => 'Valid First Aid/CPR Certificates', 'response_type' => 'yes_no_na', 'is_critical' => true],
            ['category' => 'documentation', 'title' => 'Current Criminal Record Checks', 'response_type' => 'yes_no_na', 'is_critical' => true],
            ['category' => 'documentation', 'title' => 'Attendance Records Maintained', 'response_type' => 'yes_no_na', 'is_mandatory' => true],
            ['category' => 'documentation', 'title' => 'Incident/Accident Reports Filed', 'response_type' => 'yes_no_na', 'is_mandatory' => true],
            ['category' => 'documentation', 'title' => 'Emergency Contact Information Updated', 'response_type' => 'yes_no_na', 'is_critical' => true],
            ['category' => 'documentation', 'title' => 'Medication Administration Records', 'response_type' => 'yes_no_na', 'is_mandatory' => true],

            // Safety Compliance
            ['category' => 'safety', 'title' => 'Smoke Detectors Functional', 'response_type' => 'yes_no_na', 'is_critical' => true],
            ['category' => 'safety', 'title' => 'CO2 Detectors Functional', 'response_type' => 'yes_no_na', 'is_critical' => true],
            ['category' => 'safety', 'title' => 'Fire Extinguisher Valid & Accessible', 'response_type' => 'yes_no_na', 'is_critical' => true],
            ['category' => 'safety', 'title' => 'Monthly Fire Drills Conducted', 'response_type' => 'yes_no_na', 'is_mandatory' => true],
            ['category' => 'safety', 'title' => 'Emergency Evacuation Plan Posted', 'response_type' => 'yes_no_na', 'is_critical' => true],
            ['category' => 'safety', 'title' => 'First Aid Kit Fully Stocked', 'response_type' => 'yes_no_na', 'is_critical' => true],
            ['category' => 'safety', 'title' => 'Hazardous Materials Secured', 'response_type' => 'yes_no_na', 'is_critical' => true],
            ['category' => 'safety', 'title' => 'Emergency Backpack Ready', 'response_type' => 'yes_no_na', 'is_critical' => true],
            ['category' => 'safety', 'title' => 'Medications Properly Stored', 'response_type' => 'yes_no_na', 'is_critical' => true],
            ['category' => 'safety', 'title' => 'Firearms Secured (if applicable)', 'response_type' => 'yes_no_na', 'is_critical' => true],

            // Environment Standards
            ['category' => 'environment', 'title' => 'Indoor Space Clean & Organized', 'response_type' => 'yes_no_na', 'is_mandatory' => true],
            ['category' => 'environment', 'title' => 'Outdoor Play Area Safe', 'response_type' => 'yes_no_na', 'is_mandatory' => true],
            ['category' => 'environment', 'title' => 'Equipment in Good Repair', 'response_type' => 'yes_no_na', 'is_mandatory' => true],
            ['category' => 'environment', 'title' => 'Age-Appropriate Toys Available', 'response_type' => 'yes_no_na', 'is_mandatory' => true],
            ['category' => 'environment', 'title' => 'Adequate Ventilation & Lighting', 'response_type' => 'yes_no_na', 'is_mandatory' => true],
            ['category' => 'environment', 'title' => 'Temperature Appropriate', 'response_type' => 'yes_no_na', 'is_mandatory' => true],
            ['category' => 'environment', 'title' => 'Sleeping Areas Clean & Safe', 'response_type' => 'yes_no_na', 'is_mandatory' => true],

            // Health & Hygiene
            ['category' => 'health', 'title' => 'Handwashing Facilities Available', 'response_type' => 'yes_no_na', 'is_critical' => true],
            ['category' => 'health', 'title' => 'Bathroom Clean & Sanitized', 'response_type' => 'yes_no_na', 'is_mandatory' => true],
            ['category' => 'health', 'title' => 'Kitchen Meets Hygiene Standards', 'response_type' => 'yes_no_na', 'is_critical' => true],
            ['category' => 'health', 'title' => 'Food Storage Appropriate', 'response_type' => 'yes_no_na', 'is_critical' => true],
            ['category' => 'health', 'title' => 'Fridge Temperature ≤4°C', 'response_type' => 'yes_no_na', 'is_critical' => true],
            ['category' => 'health', 'title' => 'No Signs of Pest Infestation', 'response_type' => 'yes_no_na', 'is_critical' => true],
            ['category' => 'health', 'title' => 'Diaper Changing Area Sanitary', 'response_type' => 'yes_no_na', 'is_mandatory' => true],

            // Program Quality
            ['category' => 'child_care_practices', 'title' => 'Program Planning Posted', 'response_type' => 'yes_no_na', 'is_mandatory' => true],
            ['category' => 'child_care_practices', 'title' => 'Menu Posted & Nutritious', 'response_type' => 'yes_no_na', 'is_mandatory' => true],
            ['category' => 'child_care_practices', 'title' => 'Child-to-Caregiver Ratio Met', 'response_type' => 'yes_no_na', 'is_critical' => true],
            ['category' => 'child_care_practices', 'title' => 'Developmental Activities Provided', 'response_type' => 'yes_no_na', 'is_mandatory' => true],
            ['category' => 'child_care_practices', 'title' => 'Outdoor Play Time Adequate', 'response_type' => 'yes_no_na', 'is_mandatory' => true],
            ['category' => 'child_care_practices', 'title' => 'Rest/Quiet Time Provided', 'response_type' => 'yes_no_na', 'is_mandatory' => true],

            // Professional Practice
            ['category' => 'staff_qualifications', 'title' => 'Professional Development Up-to-date', 'response_type' => 'yes_no_na', 'is_mandatory' => true],
            ['category' => 'staff_qualifications', 'title' => 'Policies & Procedures Available', 'response_type' => 'yes_no_na', 'is_mandatory' => true],
            ['category' => 'staff_qualifications', 'title' => 'Parent Communication Maintained', 'response_type' => 'yes_no_na', 'is_mandatory' => true],
            ['category' => 'staff_qualifications', 'title' => 'Insurance Coverage Current', 'response_type' => 'yes_no_na', 'is_critical' => true],
        ];

        foreach ($items as $index => $item) {
            $isCritical = ($item['is_critical'] ?? false);

            // Build attributes without the legacy 'is_critical' key (column removed)
            $attrs = [
                'sort_order' => $index + 1,
                'points_possible' => $isCritical ? 5 : 3,
                'requires_comment' => $isCritical,
                'is_active' => true,
                'description' => 'Compliance check: ' . $item['title'],
                'is_critical_compliance' => $isCritical,
            ];

            // Merge only the allowed insertable fields
            $insert = array_merge(
                [
                    'title' => $item['title'],
                    'category' => $item['category'],
                    'response_type' => $item['response_type'] ?? 'yes_no_na',
                ],
                $attrs
            );

            InspectionItem::updateOrCreate(
                ['checklist_id' => $checklist->id, 'code' => 'COMP-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT)],
                $insert
            );
        }

        $checklist->updateTotalItems();
    }
}
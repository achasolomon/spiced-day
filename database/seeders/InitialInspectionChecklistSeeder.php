<?php

namespace Database\Seeders;

use App\Models\InspectionChecklist;
use App\Models\InspectionItem;
use Illuminate\Database\Seeder;

class InitialInspectionChecklistSeeder extends Seeder
{
    public function run()
    {
        $checklist = InspectionChecklist::updateOrCreate(
            ['slug' => 'initial-visit-check'],
            [
                'name' => 'SPICE\'d Initial Visit Check',
                'description' => 'Initial safety and environment check for new dayhome providers.',
                'inspection_type' => 'initial_inspection',
                'dayhome_type' => 'all',
                'is_active' => true,
                'is_default' => true,
                'passing_score' => 90.00,
                'estimated_duration' => 120,
                'instructions' => 'Complete all items. Mark N/A only if not applicable. Items marked as critical must pass to avoid application rejection.',
            ]
        );

        // Define which items are in the 60-item second/final inspection checklist
        // Items NOT in this list are ONLY in initial inspection (the 40 items)
        $itemsInSecondFinalInspection = [
            'Fire Safety Equipment Installed & Tested',
            'Smoke & CO2 Detectors Working',
            'Emergency Exits Clear & Accessible',
            'First Aid Kit Present',
            'Medications Locked (Except EpiPen)',
            'Hot Water <50°C',
            'Electrical Outlets Covered',
            'Windows Have Guards (Openable by Adults)',
            'Staircases Have Solid Gates & Latches',
            'Chemicals, Alcohol, Cannabis Inaccessible',
            'Bathroom Cabinets & Medicine Drawers Latched',
            'Cleaning Products Inaccessible',
            'Sharp Objects Locked',
            'No Flammables Near Stove',
            'Fridge ≤4°C, Freezer ≤-18°C',
            'Play Area Fenced or Approved Park',
            'No Toxic Equipment Accessible',
            'No Unsupervised Access to Garage/Shed',
            'Indoor Space Meets Size Requirements',
            'Outdoor Play Area Properly Fenced',
            'All Play Equipment Age-Appropriate',
            'Sleeping Areas Meet Standards',
            'Bathroom Clean & Sanitized',
            'Kitchen Meets All Health Standards',
            'Food Storage Meets Requirements',
            'Diaper Changing Area Sanitary',
            'Handwashing Facilities Accessible',
            'No Evidence of Pests',
            'All Required Postings Displayed',
            'Emergency Contact List Posted',
            'Allergy List Posted & Current',
            'Evacuation Plan Posted & Practiced',
            'Program Planning Available',
            'Weekly Menu Posted',
            'Policies & Procedures Manual Complete',
            'Child Records Complete',
            'Emergency Backpack Fully Prepared',
            'All Cribs/Cots Meet CSA Standards',
            'Highchairs Meet CSA Standards',
            'First Aid & CPR Certified',
            'Food Handler Certification',
            'Criminal Record Check Completed',
            'Child Intervention Check Clear',
            'References Verified',
            'Liability Insurance Valid',
            'License Application Complete',
            'All Fees Paid',
            'Fire Drill Log (Monthly)',
            'Emergency Procedures Understood',
            'Fire Extinguisher Present & Known',
            'Portable Records Binder',
            'Medication Forms & Allergy List',
            'Evacuation Plan & Emergency Numbers',
            'Emergency Plan Posted',
            'Emergency Contacts Posted',
            'Allergies Posted',
            'Menu Posted',
            'Program Planning Posted',
            'Proper Waste Disposal',
            'Education/Training Certificates',
        ];

        $items = [
            // Walls and Windows
            ['category' => 'safety', 'title' => 'Electrical Outlets Covered', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'safety', 'title' => 'Electrical Cords Maintained & Out of Reach', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'safety', 'title' => 'Windows Have Guards (Openable by Adults)', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'environment', 'title' => 'Windows in Every Room for Ventilation', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'safety', 'title' => 'Drapery Cords Secured', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'environment', 'title' => 'Walls Clean, No Chipped Paint', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],

            // Indoor Play Area
            ['category' => 'safety', 'title' => 'Staircases Have Solid Gates & Latches', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'safety', 'title' => 'Handrails & Side Protection on Stairs', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'safety', 'title' => 'Shelves: Items Out of Reach', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'safety', 'title' => 'Non-Child Cabinets Latched', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'safety', 'title' => 'Chemicals, Alcohol, Cannabis Inaccessible', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'environment', 'title' => 'Floors Clean & Clutter-Free', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'environment', 'title' => 'Rugs/Mats Clean & Secured', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'equipment', 'title' => 'Toys in Good Repair, Age-Appropriate', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'equipment', 'title' => 'Sufficient Variety & Quantity of Toys', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'safety', 'title' => 'Play Space Free of Hazards', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'safety', 'title' => 'Poisonous Plants Out of Reach', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'safety', 'title' => 'Safety Guard on Fireplace (if applicable)', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'environment', 'title' => 'Residence Clean & Sufficient Space', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'environment', 'title' => 'Toys Organized & Accessible', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'environment', 'title' => 'Furnishings Dusted & Clean', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'equipment', 'title' => 'Children\'s Furniture Safe & Clean', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'safety', 'title' => 'Sharp Corners Covered', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'equipment', 'title' => 'Cribs/Cots Comply with Regulations', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],

            // Outdoor Play Area
            ['category' => 'environment', 'title' => 'Play Area Fenced or Approved Park', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'equipment', 'title' => 'Equipment Safe & Age-Appropriate', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'safety', 'title' => 'No Toxic Equipment Accessible', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'safety', 'title' => 'No Protruding Nails/Bolts', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'equipment', 'title' => 'Strollers/Wagons in Good Repair', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'safety', 'title' => 'No Standing Water', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'safety', 'title' => 'BBQ & Utensils Stored Safely', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'safety', 'title' => 'Balconies Have Barriers', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'safety', 'title' => 'No Unsupervised Access to Garage/Shed', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'environment', 'title' => 'No Garbage/Debris', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'equipment', 'title' => 'Equipment in Good Repair', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],

            // Bathroom
            ['category' => 'safety', 'title' => 'Cabinets & Medicine Drawers Latched', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'safety', 'title' => 'Cleaning Products Inaccessible', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'safety', 'title' => 'Toiletries Out of Reach', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'safety', 'title' => 'Change Table Supervised', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'health', 'title' => 'Bathroom Accessible & Supervised', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'health', 'title' => 'Separate Towels per Child', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'health', 'title' => 'Surfaces Clean & Sanitized', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'health', 'title' => 'Age-Appropriate Toileting Items', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'health', 'title' => 'Drains & Toilets Unclogged', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'safety', 'title' => 'Hot Water <50°C', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],

            // Kitchen
            ['category' => 'safety', 'title' => 'Kitchen Gated (if possible)', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'health', 'title' => 'Appliances Clean', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'health', 'title' => 'Drains/Sinks Unclogged', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'safety', 'title' => 'Electrical Appliances Inaccessible', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'safety', 'title' => 'Sharp Objects Locked', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'safety', 'title' => 'No Flammables Near Stove', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'health', 'title' => 'Fridge Clean', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'health', 'title' => 'Countertops Clean & Organized', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'equipment', 'title' => 'Highchairs Meet CSA Standards', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'health', 'title' => 'Fridge ≤4°C, Freezer ≤-18°C', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'staff_qualifications', 'title' => 'Food Handler Certification', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'health', 'title' => 'No Signs of Pests', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],

            // Emergency
            ['category' => 'emergency_procedures', 'title' => 'First Aid Kit Present', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'documentation', 'title' => 'Portable Records Binder', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'documentation', 'title' => 'Medication Forms & Allergy List', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'emergency_procedures', 'title' => 'Evacuation Plan & Emergency Numbers', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],

            // Other
            ['category' => 'safety', 'title' => 'Medications Locked (Except EpiPen)', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'safety', 'title' => 'Fire Extinguisher Present & Known', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'safety', 'title' => 'Smoke & CO2 Detectors Working', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'staff_qualifications', 'title' => 'First Aid & CPR Certified', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'emergency_procedures', 'title' => 'Fire Drill Log (Monthly)', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'environment', 'title' => 'Entrances Clear of Clutter', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],
            ['category' => 'environment', 'title' => 'Children Have Designated Space', 'response_type' => 'yes_no_na', 'included_in_second_final' => false],

            // Postings
            ['category' => 'documentation', 'title' => 'Menu Posted', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'documentation', 'title' => 'Program Planning Posted', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'emergency_procedures', 'title' => 'Emergency Plan Posted', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'documentation', 'title' => 'Emergency Contacts Posted', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
            ['category' => 'documentation', 'title' => 'Allergies Posted', 'response_type' => 'yes_no_na', 'included_in_second_final' => true],
        ];

        foreach ($items as $index => $item) {
            // Items included in second/final are NOT critical in initial
            // Items NOT included in second/final ARE critical in initial
            $isCritical = !($item['included_in_second_final'] ?? false);
            
            InspectionItem::updateOrCreate(
                ['checklist_id' => $checklist->id, 'code' => 'INIT-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT)],
                [
                    'title' => $item['title'],
                    'category' => $item['category'],
                    'response_type' => $item['response_type'],
                    'sort_order' => $index + 1,
                    'is_critical_initial' => $isCritical,
                    'is_mandatory' => true,
                    'points_possible' => $isCritical ? 5 : 3,
                    'requires_comment' => $isCritical,
                    'is_active' => true,
                    'included_in_initial' => true,
                    'included_in_second' => $item['included_in_second_final'] ?? false,
                    'included_in_final' => $item['included_in_second_final'] ?? false,
                    'included_in_compliance' => $item['included_in_second_final'] ?? false,
                    'description' => $item['title'],
                ]
            );
        }

        $checklist->updateTotalItems();
        
        $this->command->info('Initial inspection checklist created!');
        $this->command->info('Critical items (not in second/final): ' . count(array_filter($items, fn($i) => !($i['included_in_second_final'] ?? false))));
        $this->command->info('Non-critical items (in second/final): ' . count(array_filter($items, fn($i) => ($i['included_in_second_final'] ?? false))));
    }
}
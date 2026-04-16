<?php
// =================================================================
// Migration 9: create_inspection_items_table.php
// =================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_id')->constrained('inspection_checklists')->onDelete('cascade');
            
            // Item Details
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description');
            $table->text('criteria')->nullable();
            $table->text('instructions')->nullable();
            
            // Classification
            $table->enum('category', [
                'safety',
                'health',
                'environment',
                'documentation',
                'equipment',
                'staff_qualifications',
                'emergency_procedures',
                'child_care_practices',
                'nutrition',
                'transportation',
                'administration'
            ]);
            
            $table->string('subcategory')->nullable();
            
            // Assessment
            $table->enum('response_type', [
                'yes_no',
                'yes_no_na',
                'rating_scale',
                'checklist',
                'numeric',
                'text',
                'photo_required'
            ])->default('yes_no');
            
            $table->json('response_options')->nullable();
            $table->boolean('requires_photo')->default(false);
            $table->boolean('requires_comment')->default(false);
            
            // Scoring and Importance
            $table->integer('weight')->default(1);
            
            // NEW: Dynamic critical status based on inspection type
            $table->boolean('is_critical_initial')->default(false); // Critical for initial inspection
            $table->boolean('is_critical_second')->default(true); // All items critical for second
            $table->boolean('is_critical_final')->default(true); // All items critical for final
            $table->boolean('is_critical_compliance')->default(true); // All items critical for compliance
            
            $table->boolean('is_mandatory')->default(true);
            $table->decimal('points_possible', 5, 2)->default(1.00);
            
            // NEW: Inspection Stage Applicability
            $table->boolean('included_in_initial')->default(true);
            $table->boolean('included_in_second')->default(false); // Only 60 items for second/final
            $table->boolean('included_in_final')->default(false); // Same 60 items as second
            $table->boolean('included_in_compliance')->default(false); // Same 60 items
            
            // Conditions
            $table->json('applicable_when')->nullable();
            $table->json('not_applicable_when')->nullable();
            
            // Help and References
            $table->text('help_text')->nullable();
            $table->json('reference_documents')->nullable();
            $table->string('regulation_reference')->nullable();
            
            // Organization
            $table->string('section')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['checklist_id', 'sort_order']);
            $table->index(['category', 'is_active']);
            $table->index(['included_in_initial', 'included_in_second', 'included_in_final'],     'inspection_items_inclusion_idx'
        );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_items');
    }
};
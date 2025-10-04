<?php
// =================================================================
// MIGRATION 9: create_inspection_items_table.php
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
            $table->boolean('is_critical')->default(false);
            $table->boolean('is_mandatory')->default(true);
            $table->decimal('points_possible', 5, 2)->default(1.00);
            
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
            $table->index(['is_critical', 'is_mandatory']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_items');
    }
};

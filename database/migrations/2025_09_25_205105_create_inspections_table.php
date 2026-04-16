<?php
// =================================================================
// Migration 7: create_inspections_table.php
// =================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('consultant_id')->constrained('users')->onDelete('cascade');
            
            // Inspection Details
            $table->enum('type', [
                'initial_inspection',
                'second_inspection', 
                'final_inspection',
                'compliance_inspection_scheduled',
                'compliance_inspection_unscheduled',
                'complaint_inspection',
                'renewal_inspection'
            ]);
            
            $table->string('inspection_number')->unique();
            $table->datetime('conducted_at')->nullable(); // Nullable for drafts
            $table->integer('duration')->nullable();
            
            // Draft Status
            $table->boolean('is_draft')->default(false);
            $table->datetime('draft_saved_at')->nullable();
            
            // Results
            $table->enum('overall_result', ['pass', 'conditional_pass', 'fail', 'incomplete'])->nullable();
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->integer('items_checked')->default(0);
            $table->integer('items_passed')->default(0);
            $table->integer('items_failed')->default(0);
            $table->integer('items_not_applicable')->default(0);
            
            // Checklist Data
            $table->json('checklist_results')->nullable();
            $table->json('failed_items')->nullable();
            $table->json('critical_failed_items')->nullable(); // NEW: Track critical failures separately
            $table->json('recommendations')->nullable();
            $table->json('required_actions')->nullable();
            
            // Follow-up Decision (for second/final inspections)
            $table->enum('consultant_decision', [
                'proceed_to_next_stage',
                'schedule_follow_up',
                'reject_application'
            ])->nullable();
            $table->text('decision_notes')->nullable();
            
            // Follow-up
            $table->date('follow_up_required_by')->nullable();
            $table->boolean('requires_reinspection')->default(false);
            $table->date('reinspection_date')->nullable();
            $table->text('follow_up_notes')->nullable();
            
            // Documentation (REQUIRED FIELDS)
            $table->text('summary')->nullable(); // Will be required in validation
            $table->text('observations')->nullable(); // Will be required in validation
            $table->text('recommendations_text')->nullable();
            $table->text('consultant_notes')->nullable(); // Will be required in validation
            $table->json('photos')->nullable();
            
            // Signatures and Approval
            $table->json('signatures')->nullable();
            $table->datetime('applicant_acknowledged_at')->nullable();
            $table->boolean('is_final')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->datetime('approved_at')->nullable();
            
            // Weather and Environmental
            $table->string('weather_conditions')->nullable();
            $table->decimal('temperature', 5, 2)->nullable();
            $table->text('environmental_factors')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['application_id', 'type']);
            $table->index(['consultant_id', 'conducted_at']);
            $table->index(['overall_result', 'conducted_at']);
            $table->index(['is_draft', 'consultant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
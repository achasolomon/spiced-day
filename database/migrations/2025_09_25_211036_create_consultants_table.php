<?php
// =================================================================
// MIGRATION 10: create_consultants_table.php
// =================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Professional Details
            $table->string('employee_id')->unique();
            $table->string('department')->nullable();
            $table->string('position_title');
            $table->date('hire_date');
            $table->enum('employment_status', ['active', 'inactive', 'on_leave', 'terminated'])->default('active');
            
            // Qualifications
            $table->json('certifications')->nullable();
            $table->json('specializations')->nullable();
            $table->text('qualifications')->nullable();
            $table->json('languages')->nullable();
            
            // Work Preferences
            $table->json('service_areas')->nullable();
            $table->json('availability')->nullable();
            $table->integer('max_concurrent_applications')->default(10);
            $table->boolean('accepts_new_applications')->default(true);
            
            // Performance Metrics
            $table->integer('total_applications_handled')->default(0);
            $table->integer('completed_inspections')->default(0);
            $table->decimal('average_completion_time', 8, 2)->default(0.00);
            $table->decimal('approval_rate', 5, 2)->default(0.00);
            $table->decimal('client_satisfaction_rating', 3, 2)->default(0.00);
            
            // Current Workload
            $table->integer('active_applications')->default(0);
            $table->integer('pending_inspections')->default(0);
            $table->datetime('last_inspection_date')->nullable();
            $table->datetime('next_available_date')->nullable();
            
            // Contact and Emergency
            $table->string('work_phone')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            
            // System Access
            $table->json('permissions')->nullable();
            $table->boolean('can_approve_applications')->default(false);
            $table->boolean('can_conduct_inspections')->default(true);
            $table->boolean('can_view_all_applications')->default(false);
            
            // Notes
            $table->text('bio')->nullable();
            $table->text('internal_notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['employment_status', 'accepts_new_applications']);
            $table->index('next_available_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultants');
    }
};


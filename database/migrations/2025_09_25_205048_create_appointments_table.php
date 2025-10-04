<?php
// =================================================================
// MIGRATION 4: create_appointments_table.php
// =================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('consultant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('applicant_id')->constrained('users')->onDelete('cascade');
            
            // Appointment Details
            $table->enum('type', [
                'meet_and_greet',
                'initial_inspection', 
                'second_inspection',
                'final_inspection',
                'contract_signing',
                'follow_up'
            ]);
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->datetime('scheduled_at');
            $table->datetime('ends_at');
            $table->integer('duration')->default(120);
            
            // Location
            $table->text('location_address');
            $table->string('location_type')->default('home');
            $table->text('location_notes')->nullable();
            
            // Status
            $table->enum('status', [
                'scheduled',
                'confirmed',
                'in_progress',
                'completed',
                'cancelled',
                'rescheduled',
                'no_show'
            ])->default('scheduled');
            
            // Confirmation
            $table->datetime('confirmed_at')->nullable();
            $table->string('confirmation_method')->nullable();
            $table->boolean('applicant_confirmed')->default(false);
            $table->boolean('consultant_confirmed')->default(false);
            
            // Completion Details
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->text('outcome')->nullable();
            $table->json('checklist_results')->nullable();
            $table->enum('result', ['pass', 'fail', 'conditional', 'pending'])->nullable();
            
            // Rescheduling
            $table->foreignId('rescheduled_from')->nullable()->constrained('appointments');
            $table->text('reschedule_reason')->nullable();
            $table->integer('reschedule_count')->default(0);
            
            // Notes
            $table->text('preparation_notes')->nullable();
            $table->text('completion_notes')->nullable();
            $table->text('internal_notes')->nullable();
            
            // Reminders
            $table->json('reminder_settings')->nullable();
            $table->datetime('last_reminder_sent')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['consultant_id', 'scheduled_at']);
            $table->index(['applicant_id', 'status']);
            $table->index(['type', 'status', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
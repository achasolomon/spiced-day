
// =================================================================
// MIGRATION 2: create_applications_table.php
// =================================================================
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('consultant_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Educator Information (from user, but stored here for record)
            $table->string('educator_first_name');
            $table->string('educator_last_name');
            $table->string('email');
            $table->string('phone');
            
            // Dayhome Address
            $table->string('address_line_1');
            $table->string('city');
            $table->string('province');
            $table->string('postal_code');
            
            // Professional Details
            $table->string('childcare_level')->nullable(); // Level 1, 2, 3, etc.
            $table->string('referred_by')->nullable();
            $table->boolean('has_criminal_record_check')->default(false);
            $table->boolean('has_first_aid_cpr')->default(false);
            $table->text('languages_spoken')->nullable();
            $table->text('childcare_education')->nullable();
            
            // Home Details
            $table->integer('home_residents_count')->nullable();
            $table->text('home_residents_details')->nullable(); // Names, ages, occupations
            $table->enum('smoking_status', ['no', 'yes_please_specify'])->nullable();
            $table->string('smoking_details')->nullable();
            $table->boolean('has_pets')->default(false);
            $table->string('pets_details')->nullable();
            
            // Current Dayhome Operation
            $table->boolean('currently_operating')->default(false);
            $table->text('current_operation_details')->nullable();
            $table->boolean('evening_overnight_care')->default(false);
            $table->enum('home_type', ['apartment', 'duplex', 'house', 'townhouse'])->nullable();
            $table->enum('home_ownership', ['rent', 'own'])->nullable();
            
            // Goals and Philosophy
            $table->date('desired_start_date')->nullable();
            $table->boolean('comfortable_special_needs')->default(false);
            $table->text('motivation')->nullable(); // Why become educator
            $table->text('why_spiced')->nullable(); // Why SPICE'd specifically
            $table->text('education_philosophy')->nullable();
            $table->boolean('fenced_backyard')->default(false);
            $table->text('program_planning_process')->nullable();
            
            // Application Status & Workflow
            $table->enum('status', [
                'draft',
                'submitted',
                'phone_interview_scheduled',
                'phone_interview_completed',
                'meet_and_greet_scheduled',
                'meet_and_greet_completed',
                'initial_inspection_scheduled',
                'initial_inspection_completed',
                'documents_pending',
                'documents_submitted',
                'second_inspection_scheduled',
                'second_inspection_completed',
                'contract_signing_scheduled',
                'contract_signed',
                'approved',
                'rejected',
                'cancelled'
            ])->default('draft');
            
            $table->enum('current_stage', [
                'intake',
                'phone_interview',
                'meet_and_greet',
                'initial_inspection',
                'document_collection',
                'second_inspection',
                'contract_signing',
                'approved'
            ])->default('intake');
            
            // Progress & Admin
            $table->decimal('completion_percentage', 5, 2)->default(0.00);
            $table->json('requirements_checklist')->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->date('license_expires_at')->nullable();
            $table->string('certificate_number')->nullable()->unique();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['status', 'current_stage']);
            $table->index(['consultant_id', 'status']);
            $table->index('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};

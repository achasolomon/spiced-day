<?php

// =================================================================
// MIGRATION 5: create_documents_table.php
// =================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('document_requirement_id')->nullable()->constrained()->onDelete('set null');
            
            // Document Details
            $table->string('name');
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('file_type');
            $table->string('mime_type');
            $table->bigInteger('file_size');
            $table->string('file_hash')->nullable();
            
            // Document Classification
            $table->enum('category', [
                'educator_certificate',
                'cpr_first_aid',
                'criminal_record_check',
                'statement_of_disclosure',
                'fit_to_work_assessment',
                'home_insurance',
                'food_handler_certificate',
                'evacuation_plan',
                'emergency_contacts',
                'daily_schedule',
                'program_planning',
                'menu_sample',
                'transportation_agreement',
                'cleaning_checklist',
                'child_safety_training',
                'character_references',
                'fee_schedule',
                'transportation_policy',
                'critical_incidents',
                'car_insurance',
                'liability_insurance',
                'pet_vaccination',
                'other'
            ]);
            
            $table->enum('type', [
                'required_document',
                'supporting_document', 
                'inspection_photo',
                'certificate',
                'form',
                'agreement',
                'policy',
                'reference'
            ])->default('required_document');
            
            // Status and Review
            $table->enum('status', [
                'uploaded',
                'under_review', 
                'approved',
                'rejected',
                'expired',
                'replacement_required'
            ])->default('uploaded');
            
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            
            // Document Validity
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('expires')->default(false);
            $table->integer('validity_period')->nullable();
            
            // Version Control
            $table->integer('version')->default(1);
            $table->foreignId('replaces_document_id')->nullable()->constrained('documents');
            $table->boolean('is_current_version')->default(true);
            
            // Metadata
            $table->json('metadata')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_sensitive')->default(false);
            $table->integer('download_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['application_id', 'category', 'status']);
            $table->index(['category', 'expiry_date']);
            $table->index(['uploaded_by', 'created_at']);
            $table->index('file_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
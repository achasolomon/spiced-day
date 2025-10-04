<?php
// =================================================================
// MIGRATION 6: create_document_requirements_table.php
// =================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_requirements', function (Blueprint $table) {
            $table->id();
            
            // Requirement Details
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('instructions')->nullable();
            
            // Classification
            $table->string('category');
            $table->enum('stage', [
                'application',
                'meet_and_greet',
                'initial_inspection', 
                'document_submission',
                'second_inspection',
                'contract_signing'
            ]);
            
            // Requirements
            $table->boolean('is_required')->default(true);
            $table->boolean('is_conditional')->default(false);
            $table->json('conditions')->nullable();
            $table->json('accepted_formats')->nullable();
            $table->integer('max_file_size')->nullable();
            $table->integer('max_files')->default(1);
            
            // Validity
            $table->boolean('has_expiry')->default(false);
            $table->integer('validity_period')->nullable();
            $table->boolean('requires_annual_renewal')->default(false);
            
            // Processing
            $table->boolean('requires_review')->default(true);
            $table->integer('review_priority')->default(5);
            $table->json('review_criteria')->nullable();
            $table->text('rejection_reasons')->nullable();
            
            // Display
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('icon')->nullable();
            $table->string('help_text')->nullable();
            $table->string('example_url')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['stage', 'is_required', 'sort_order']);
            $table->index(['category', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_requirements');
    }
};
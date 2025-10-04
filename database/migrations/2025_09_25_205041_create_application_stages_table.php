<?php
// =================================================================
// MIGRATION 3: create_application_stages_table.php
// =================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('stage_name');
            $table->string('stage_title');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'rejected', 'skipped'])->default('pending');
            $table->text('description')->nullable();
            $table->json('data')->nullable();
            
            // Timing
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->datetime('due_date')->nullable();
            $table->integer('estimated_duration')->nullable();
            
            // Stage Requirements
            $table->json('requirements')->nullable();
            $table->json('completed_requirements')->nullable();
            $table->decimal('completion_percentage', 5, 2)->default(0.00);
            
            // Notes and Comments
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            
            // Ordering
            $table->integer('sort_order')->default(0);
            $table->boolean('is_milestone')->default(false);
            $table->boolean('requires_approval')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['application_id', 'sort_order']);
            $table->index(['status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_stages');
    }
};
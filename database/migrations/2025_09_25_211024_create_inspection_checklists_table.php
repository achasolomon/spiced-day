<?php
// =================================================================
// MIGRATION 8: create_inspection_checklists_table.php
// =================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspection_checklists', function (Blueprint $table) {
            $table->id();
            
            // Checklist Details
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('version')->default('1.0');
            
            // Usage
            $table->enum('inspection_type', [
                'initial_inspection',
                'second_inspection',
                'final_inspection', 
                'follow_up_inspection',
                'renewal_inspection',
                'complaint_inspection'
            ]);
            
            $table->enum('dayhome_type', ['family', 'group', 'nursery', 'all'])->default('all');
            
            // Configuration
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('total_items')->default(0);
            $table->json('scoring_system')->nullable();
            $table->decimal('passing_score', 5, 2)->default(80.00);
            
            // Metadata
            $table->text('instructions')->nullable();
            $table->json('required_materials')->nullable();
            $table->integer('estimated_duration')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['inspection_type', 'is_active']);
            $table->index(['dayhome_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_checklists');
    }
};
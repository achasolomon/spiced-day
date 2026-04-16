<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('educator_profile_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('educator_profile_id')->constrained()->onDelete('cascade');
            
            // Field Details
            $table->string('title'); // e.g., "Montessori Certification"
            $table->enum('type', ['document', 'text', 'date', 'boolean'])->default('text');
            $table->text('value')->nullable(); // For text type
            $table->string('file_path')->nullable(); // For document type
            $table->string('file_name')->nullable(); // Original filename
            $table->date('date_value')->nullable(); // For date type
            $table->boolean('boolean_value')->nullable(); // For yes/no type
            $table->date('expiry_date')->nullable(); // Optional expiry
            $table->text('notes')->nullable(); // Additional context
            
            // Metadata
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['educator_profile_id', 'is_active']);
            $table->index('sort_order');
            $table->index('expiry_date'); // For finding expiring items
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('educator_profile_items');
    }
};
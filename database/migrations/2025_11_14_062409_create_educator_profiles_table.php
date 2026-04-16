<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('educator_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            
            // Core Profile Fields (Not in application)
            $table->text('professional_bio')->nullable();
            $table->time('operating_hours_start')->nullable();
            $table->time('operating_hours_end')->nullable();
            $table->integer('current_capacity')->default(0); // Current enrolled children
            $table->integer('maximum_capacity')->nullable(); // Licensed capacity
            $table->json('specializations')->nullable(); // Array of specializations
            $table->text('professional_goals')->nullable();
            
            // Additional Info
            $table->string('profile_photo')->nullable();
            $table->boolean('is_complete')->default(false);
            $table->timestamp('last_updated_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('user_id');
            $table->index('is_complete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('educator_profiles');
    }
};
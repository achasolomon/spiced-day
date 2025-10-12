<?php
// database/migrations/YYYY_MM_DD_HHMMSS_create_postal_code_ranges_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postal_code_ranges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->onDelete('cascade');
            $table->string('prefix', 6)->index(); // e.g., "M5A", "K1A"
            $table->text('full_postal_codes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postal_code_ranges');
    }
};

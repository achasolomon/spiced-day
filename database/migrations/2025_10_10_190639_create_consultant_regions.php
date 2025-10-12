<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultant_regions', function (Blueprint $table) {
            $table->foreignId('consultant_id')->constrained()->onDelete('cascade');
            $table->foreignId('region_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->primary(['consultant_id', 'region_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultant_regions');
    }
};

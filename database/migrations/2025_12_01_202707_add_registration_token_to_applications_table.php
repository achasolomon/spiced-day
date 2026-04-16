<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('registration_token', 100)->nullable()->unique()->after('anonymous_token');
            $table->timestamp('registration_token_expires_at')->nullable()->after('registration_token');
            $table->index('registration_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex(['registration_token']);
            $table->dropColumn(['registration_token', 'registration_token_expires_at']);
        });
    }
};

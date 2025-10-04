<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add the token column (6 characters for a numeric code) and make it nullable
            $table->string('email_verification_token', 6)->nullable()->after('email');
            
            // Note: your 'email_verified_at' should already exist, but if not, ensure it's here:
            // $table->timestamp('email_verified_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email_verification_token');
        });
    }
};
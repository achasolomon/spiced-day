<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Make user_id nullable to allow anonymous applications
            $table->foreignId('user_id')->nullable()->change();
            
            // Add token for tracking anonymous applications
            $table->string('anonymous_token', 64)->nullable()->unique()->after('user_id');
            
            // Add flag to track if account was created
            $table->boolean('account_created')->default(false)->after('anonymous_token');
            
            // Add timestamp for when account was created
            $table->timestamp('account_created_at')->nullable()->after('account_created');
            
            // Add index for token lookups
            $table->index('anonymous_token');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex(['anonymous_token']);
            $table->dropColumn(['anonymous_token', 'account_created', 'account_created_at']);
            
            // Note: Making user_id NOT NULL again would require data cleanup first
        });
    }
};
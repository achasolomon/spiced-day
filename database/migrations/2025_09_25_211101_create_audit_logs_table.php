<?php

// =================================================================
// MIGRATION 12: create_audit_logs_table.php
// =================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('application_id')->nullable()->constrained()->onDelete('cascade');
            
            // Action Details
            $table->string('action');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('description');
            
            // Context
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable();
            
            // Request Information
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('session_id')->nullable();
            $table->string('request_id')->nullable();
            
            // Classification
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->enum('category', [
                'authentication',
                'application_management', 
                'document_management',
                'inspection',
                'appointment',
                'user_management',
                'system_configuration',
                'data_export',
                'security',
                'other'
            ])->default('other');
            
            // Compliance
            $table->boolean('is_sensitive')->default(false);
            $table->string('compliance_tags')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'created_at']);
            $table->index(['application_id', 'action']);
            $table->index(['model_type', 'model_id']);
            $table->index(['category', 'severity', 'created_at']);
            $table->index(['ip_address', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
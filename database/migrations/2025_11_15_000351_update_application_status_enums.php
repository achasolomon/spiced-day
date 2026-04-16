<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update applications table status enum
        DB::statement("ALTER TABLE applications MODIFY COLUMN status ENUM(
            'draft','submitted','meet_and_greet_scheduled','meet_and_greet_completed',
            'initial_inspection_scheduled','initial_inspection_completed','documents_pending',
            'documents_submitted','documents_approved','second_inspection_scheduled',
            'second_inspection_completed','final_inspection_scheduled','final_inspection_completed',
            'final_inspection_passed','final_inspection_failed','contract_signing_scheduled',
            'contract_signed','approved','active','compliance_inspection_due',
            'compliance_inspection_scheduled','compliance_inspection_completed',
            'suspended','under_review','remediation_required','rejected','cancelled','terminated'
        ) DEFAULT 'draft'");
        
        // Update applications table current_stage enum  
        DB::statement("ALTER TABLE applications MODIFY COLUMN current_stage ENUM(
            'application','meet_and_greet','initial_inspection','document_submission',
            'second_inspection','final_inspection','contract_signing','approval',
            'active','compliance_monitoring','suspended','rejected','cancelled','terminated'
        ) DEFAULT 'application'");
    }

    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE applications MODIFY COLUMN status ENUM(
            'draft','submitted','meet_and_greet_scheduled','meet_and_greet_completed',
            'initial_inspection_scheduled','initial_inspection_completed','documents_pending',
            'documents_submitted','documents_approved','second_inspection_scheduled',
            'second_inspection_completed','contract_signing_scheduled','contract_signed',
            'approved','rejected','cancelled'
        ) DEFAULT 'draft'");
        
        DB::statement("ALTER TABLE applications MODIFY COLUMN current_stage ENUM(
            'application','meet_and_greet','initial_inspection','document_submission',
            'second_inspection','contract_signing','rejected','cancelled'
        ) DEFAULT 'application'");
    }
};
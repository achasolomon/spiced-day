<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // For MySQL, we need to alter the ENUM column
        DB::statement("ALTER TABLE applications MODIFY COLUMN status ENUM(
            'draft',
            'submitted',
            'meet_and_greet_scheduled',
            'meet_and_greet_completed',
            'initial_inspection_scheduled',
            'initial_inspection_completed',
            'documents_pending',
            'documents_submitted',
            'documents_approved',
            'second_inspection_scheduled',
            'second_inspection_completed',
            'contract_signing_scheduled',
            'contract_signed',
            'approved',
            'rejected',
            'cancelled'
        ) DEFAULT 'draft'");
    }

    public function down(): void
    {
        // Revert to original ENUM values
        DB::statement("ALTER TABLE applications MODIFY COLUMN status ENUM(
            'draft',
            'submitted',
            'phone_interview_scheduled',
            'phone_interview_completed',
            'meet_and_greet_scheduled',
            'meet_and_greet_completed',
            'initial_inspection_scheduled',
            'initial_inspection_completed',
            'documents_pending',
            'documents_submitted',
            'second_inspection_scheduled',
            'second_inspection_completed',
            'contract_signing_scheduled',
            'contract_signed',
            'approved',
            'rejected',
            'cancelled'
        ) DEFAULT 'draft'");
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Dayhome operation fields
            $table->timestamp('activated_at')->nullable()->after('approved_at');
            $table->date('next_compliance_inspection_due')->nullable()->after('activated_at');
            $table->timestamp('last_compliance_inspection_at')->nullable()->after('next_compliance_inspection_due');
            $table->timestamp('suspended_at')->nullable()->after('last_compliance_inspection_at');
            $table->timestamp('terminated_at')->nullable()->after('suspended_at');
            $table->date('remediation_deadline')->nullable()->after('terminated_at');
            $table->text('remediation_notes')->nullable()->after('remediation_deadline');
            
            // Indexes for performance
            $table->index('activated_at');
            $table->index('next_compliance_inspection_due');
            $table->index('suspended_at');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'activated_at',
                'next_compliance_inspection_due',
                'last_compliance_inspection_at',
                'suspended_at',
                'terminated_at',
                'remediation_deadline',
                'remediation_notes',
            ]);
        });
    }
};
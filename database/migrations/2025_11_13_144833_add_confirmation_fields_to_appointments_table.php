<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('confirmation_token')->nullable()->after('applicant_confirmed');
            $table->timestamp('confirmation_token_expires_at')->nullable()->after('confirmation_token');
            $table->timestamp('applicant_confirmed_at')->nullable()->after('confirmation_token_expires_at');
            $table->boolean('phone_confirmation_required')->default(false)->after('applicant_confirmed_at');
        });
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn([
                'confirmation_token',
                'confirmation_token_expires_at', 
                'applicant_confirmed_at',
                'phone_confirmation_required',
            ]);
        });
    }
};
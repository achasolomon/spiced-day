<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->unsignedBigInteger('imported_by_consultant')->nullable()->after('consultant_id');
            $table->timestamp('imported_at')->nullable()->after('imported_by_consultant');

            $table->foreign('imported_by_consultant')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'imported_by_consultant')) {
                $table->dropForeign(['imported_by_consultant']);
                $table->dropColumn('imported_by_consultant');
            }
            if (Schema::hasColumn('applications', 'imported_at')) {
                $table->dropColumn('imported_at');
            }
        });
    }
};

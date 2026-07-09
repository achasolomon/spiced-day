<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dayhome_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->string('direction'); // 'outbound' or 'inbound'
            $table->string('endpoint');
            $table->integer('http_status')->nullable();
            $table->json('request_payload')->nullable();
            $table->json('response_body')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index(['application_id', 'direction']);
            $table->index('synced_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dayhome_sync_logs');
    }
};

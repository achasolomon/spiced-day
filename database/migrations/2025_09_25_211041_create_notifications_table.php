<?php
// =================================================================
// MIGRATION 11: create_notifications_table.php
// =================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('application_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Notification Details
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            
            // Delivery
            $table->enum('channel', ['database', 'email', 'sms', 'push'])->default('database');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            
            // Status
            $table->boolean('is_read')->default(false);
            $table->datetime('read_at')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->datetime('sent_at')->nullable();
            $table->json('delivery_status')->nullable();
            
            // Action
            $table->string('action_url')->nullable();
            $table->string('action_text')->nullable();
            $table->boolean('requires_action')->default(false);
            $table->datetime('action_taken_at')->nullable();
            
            // Scheduling
            $table->datetime('scheduled_for')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->json('recurring_settings')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['user_id', 'is_read', 'created_at']);
            $table->index(['type', 'scheduled_for']);
            $table->index(['application_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
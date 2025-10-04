<?php
// app/Models/Notification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'application_id',
        'created_by',
        'type',
        'title',
        'message',
        'data',
        'channel',
        'priority',
        'is_read',
        'read_at',
        'is_sent',
        'sent_at',
        'delivery_status',
        'action_url',
        'action_text',
        'requires_action',
        'action_taken_at',
        'scheduled_for',
        'is_recurring',
        'recurring_settings',
    ];

    protected $casts = [
        'data' => 'json',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'is_sent' => 'boolean',
        'sent_at' => 'datetime',
        'delivery_status' => 'json',
        'requires_action' => 'boolean',
        'action_taken_at' => 'datetime',
        'scheduled_for' => 'datetime',
        'is_recurring' => 'boolean',
        'recurring_settings' => 'json',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_for');
    }

    public function scopeDue($query)
    {
        return $query->where('scheduled_for', '<=', now());
    }

    // Helper methods
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function markActionTaken()
    {
        $this->update(['action_taken_at' => now()]);
    }

    public function isOverdue()
    {
        return $this->requires_action && 
               !$this->action_taken_at && 
               $this->created_at->addDays(7)->isPast();
    }
}
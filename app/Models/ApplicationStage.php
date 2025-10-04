<?php

// app/Models/ApplicationStage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'user_id',
        'stage_name',
        'stage_title',
        'status',
        'description',
        'data',
        'started_at',
        'completed_at',
        'due_date',
        'estimated_duration',
        'requirements',
        'completed_requirements',
        'completion_percentage',
        'notes',
        'internal_notes',
        'sort_order',
        'is_milestone',
        'requires_approval',
    ];

    protected $casts = [
        'data' => 'json',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'due_date' => 'datetime',
        'requirements' => 'json',
        'completed_requirements' => 'json',
        'completion_percentage' => 'decimal:2',
        'is_milestone' => 'boolean',
        'requires_approval' => 'boolean',
    ];

    // Relationships
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeMilestones($query)
    {
        return $query->where('is_milestone', true);
    }

    // Helper methods
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast() && !$this->isCompleted();
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completion_percentage' => 100.00
        ]);
    }
}
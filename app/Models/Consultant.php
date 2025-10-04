<?php

// app/Models/Consultant.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consultant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'employee_id',
        'department',
        'position_title',
        'hire_date',
        'employment_status',
        'certifications',
        'specializations',
        'qualifications',
        'languages',
        'service_areas',
        'availability',
        'max_concurrent_applications',
        'accepts_new_applications',
        'total_applications_handled',
        'completed_inspections',
        'average_completion_time',
        'approval_rate',
        'client_satisfaction_rating',
        'active_applications',
        'pending_inspections',
        'last_inspection_date',
        'next_available_date',
        'work_phone',
        'emergency_contact_name',
        'emergency_contact_phone',
        'permissions',
        'can_approve_applications',
        'can_conduct_inspections',
        'can_view_all_applications',
        'bio',
        'internal_notes',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'certifications' => 'json',
        'specializations' => 'json',
        'languages' => 'json',
        'service_areas' => 'json',
        'availability' => 'json',
        'accepts_new_applications' => 'boolean',
        'average_completion_time' => 'decimal:2',
        'approval_rate' => 'decimal:2',
        'client_satisfaction_rating' => 'decimal:2',
        'last_inspection_date' => 'datetime',
        'next_available_date' => 'datetime',
        'permissions' => 'json',
        'can_approve_applications' => 'boolean',
        'can_conduct_inspections' => 'boolean',
        'can_view_all_applications' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedApplications()
    {
        return $this->hasMany(Application::class, 'consultant_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'consultant_id');
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class, 'consultant_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('employment_status', 'active');
    }

    public function scopeAcceptingApplications($query)
    {
        return $query->where('accepts_new_applications', true)
                    ->where('employment_status', 'active');
    }

    public function scopeByServiceArea($query, $area)
    {
        return $query->whereJsonContains('service_areas', $area);
    }

    public function scopeAvailable($query, $date = null)
    {
        $date = $date ?: now();
        return $query->where('next_available_date', '<=', $date)
                    ->orWhereNull('next_available_date');
    }

    // Helper methods
    public function isAvailable()
    {
        return $this->employment_status === 'active' && 
               $this->accepts_new_applications &&
               $this->active_applications < $this->max_concurrent_applications;
    }

    public function updateWorkloadMetrics()
    {
        $this->update([
            'active_applications' => $this->assignedApplications()->active()->count(),
            'pending_inspections' => $this->appointments()
                ->whereIn('type', ['initial_inspection', 'second_inspection'])
                ->where('status', 'scheduled')
                ->count()
        ]);
    }
}

<?php
// app/Models/Appointment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'application_id',
        'consultant_id',
        'applicant_id',
        'type',
        'title',
        'description',
        'scheduled_at',
        'ends_at',
        'duration',
        'location_address',
        'location_type',
        'location_notes',
        'status',
        'confirmed_at',
        'confirmation_method',
        'applicant_confirmed',
        'consultant_confirmed',
        'started_at',
        'completed_at',
        'outcome',
        'checklist_results',
        'result',
        'rescheduled_from',
        'reschedule_reason',
        'reschedule_count',
        'preparation_notes',
        'completion_notes',
        'internal_notes',
        'reminder_settings',
        'last_reminder_sent',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'ends_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'applicant_confirmed' => 'boolean',
        'consultant_confirmed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'checklist_results' => 'json',
        'reminder_settings' => 'json',
        'last_reminder_sent' => 'datetime',
    ];

    // Relationships
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function consultant()
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }

    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function rescheduledFrom()
    {
        return $this->belongsTo(Appointment::class, 'rescheduled_from');
    }

    public function rescheduledAppointments()
    {
        return $this->hasMany(Appointment::class, 'rescheduled_from');
    }

    public function inspection()
    {
        return $this->hasOne(Inspection::class);
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now())
                    ->where('status', 'scheduled');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_at', today());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('applicant_confirmed', true)
                    ->where('consultant_confirmed', true);
    }

    // Helper methods
    public function isConfirmed()
    {
        return $this->applicant_confirmed && $this->consultant_confirmed;
    }

    public function canBeRescheduled()
    {
        return in_array($this->status, ['scheduled', 'confirmed']) && 
               $this->scheduled_at > now()->addHours(24);
    }

    public function getDurationInHours()
    {
        return $this->duration / 60;
    }
}
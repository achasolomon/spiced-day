<?php
// app/Models/Appointment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str; 

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
        'confirmation_token',
        'confirmation_token_expires_at',
        'applicant_confirmed_at',
        'phone_confirmation_required',
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
        'inspection_type',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'ends_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'confirmation_token_expires_at' => 'datetime',
        'applicant_confirmed_at' => 'datetime',
        'applicant_confirmed' => 'boolean',
        'consultant_confirmed' => 'boolean',
        'phone_confirmation_required' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'checklist_results' => 'json',
        'reminder_settings' => 'json',
        'last_reminder_sent' => 'datetime',
        'inspection_type' => 'string',
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

    public function getApplicantConfirmationUrl()
{
    return route('appointments.confirm-by-email', [
        'appointment' => $this->id,
        'token' => $this->generateConfirmationToken()
    ]);
}
  public function getRescheduleUrl()
    {
        return route('appointments.confirm-by-email', [
            'appointment' => $this->id,
            'token' => $this->generateConfirmationToken()
        ]) . '?action=reschedule';
    }
    
private function generateConfirmationToken()
{
    if (!$this->confirmation_token || !$this->confirmation_token_expires_at || $this->confirmation_token_expires_at->isPast()) {
            $this->update([
                'confirmation_token' => Str::random(60),
                'confirmation_token_expires_at' => now()->addDays(3)
            ]);
            $this->refresh();
        }
        return $this->confirmation_token;
}

  public function isConfirmationTokenValid()
    {
        return $this->confirmation_token && 
               $this->confirmation_token_expires_at &&
               $this->confirmation_token_expires_at->isFuture();
    }

    // Add this method for display name
    public function getDisplayNameAttribute()
    {
        return $this->applicant?->name ?? $this->application->educator_first_name . ' ' . $this->application->educator_last_name;
    }

    // Add this method for display initials
    public function getDisplayInitialsAttribute()
    {
        if ($this->applicant && $this->applicant->initials) {
            return $this->applicant->initials;
        }
        
        return substr($this->application->educator_first_name, 0, 1) . substr($this->application->educator_last_name, 0, 1);
    }
}
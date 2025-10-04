<?php

// app/Models/Inspection.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inspection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'application_id',
        'appointment_id',
        'consultant_id',
        'type',
        'inspection_number',
        'conducted_at',
        'duration',
        'overall_result',
        'overall_score',
        'items_checked',
        'items_passed',
        'items_failed',
        'items_not_applicable',
        'checklist_results',
        'failed_items',
        'recommendations',
        'required_actions',
        'follow_up_required_by',
        'requires_reinspection',
        'reinspection_date',
        'follow_up_notes',
        'summary',
        'observations',
        'recommendations_text',
        'consultant_notes',
        'photos',
        'signatures',
        'applicant_acknowledged_at',
        'is_final',
        'approved_by',
        'approved_at',
        'weather_conditions',
        'temperature',
        'environmental_factors',
    ];

    protected $casts = [
        'conducted_at' => 'datetime',
        'overall_score' => 'decimal:2',
        'checklist_results' => 'json',
        'failed_items' => 'json',
        'recommendations' => 'json',
        'required_actions' => 'json',
        'follow_up_required_by' => 'date',
        'requires_reinspection' => 'boolean',
        'reinspection_date' => 'date',
        'photos' => 'json',
        'signatures' => 'json',
        'applicant_acknowledged_at' => 'datetime',
        'is_final' => 'boolean',
        'approved_at' => 'datetime',
        'temperature' => 'decimal:2',
    ];

    // Relationships
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function consultant()
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePassed($query)
    {
        return $query->whereIn('overall_result', ['pass', 'conditional_pass']);
    }

    public function scopeFailed($query)
    {
        return $query->where('overall_result', 'fail');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRequiringFollowUp($query)
    {
        return $query->where('requires_reinspection', true)
                    ->orWhereNotNull('follow_up_required_by');
    }

    // Accessors
    public function getPassRateAttribute()
    {
        if ($this->items_checked == 0) {
            return 0;
        }

        return round(($this->items_passed / $this->items_checked) * 100, 2);
    }

    // Helper methods
    public function isPassed()
    {
        return in_array($this->overall_result, ['pass', 'conditional_pass']);
    }

    public function isFailed()
    {
        return $this->overall_result === 'fail';
    }

    public function requiresFollowUp()
    {
        return $this->requires_reinspection || $this->follow_up_required_by;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($inspection) {
            $inspection->inspection_number = static::generateInspectionNumber();
        });
    }

    public static function generateInspectionNumber()
    {
        $year = date('Y');
        $lastNumber = static::whereYear('created_at', $year)
            ->max('inspection_number');
        
        if ($lastNumber) {
            $sequence = intval(substr($lastNumber, -4)) + 1;
        } else {
            $sequence = 1;
        }

        return 'INS-' . $year . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}

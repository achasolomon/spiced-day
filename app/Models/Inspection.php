<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ApplicationStatus;

class Inspection extends Model
{
    use HasFactory, SoftDeletes;

    // Inspection type constants
    const TYPE_INITIAL = 'initial_inspection';
    const TYPE_SECOND = 'second_inspection'; 
    const TYPE_FINAL = 'final_inspection';
    const TYPE_COMPLIANCE_SCHEDULED = 'compliance_inspection_scheduled';
    const TYPE_COMPLIANCE_UNSCHEDULED = 'compliance_inspection_unscheduled';
    const TYPE_FOLLOW_UP = 'follow_up_inspection';
    
    protected $fillable = [
        'application_id',
        'appointment_id',
        'consultant_id',
        'type',
        'inspection_number',
        'conducted_at',
        'duration',
        'is_draft',
        'draft_saved_at',
        'overall_result',
        'overall_score',
        'items_checked',
        'items_passed',
        'items_failed',
        'items_not_applicable',
        'checklist_results',
        'failed_items',
        'critical_failed_items',
        'recommendations',
        'required_actions',
        'consultant_decision',
        'decision_notes',
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
        'draft_saved_at' => 'datetime',
        'is_draft' => 'boolean',
        'overall_score' => 'decimal:2',
        'checklist_results' => 'json',
        'failed_items' => 'json',
        'critical_failed_items' => 'json',
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

    public function scopeDrafts($query)
    {
        return $query->where('is_draft', true);
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_draft', false);
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

    public function isComplianceInspection(): bool
    {
        return in_array($this->type, [self::TYPE_COMPLIANCE_SCHEDULED, self::TYPE_COMPLIANCE_UNSCHEDULED]);
    }

    public function isInitialInspection(): bool
    {
        return $this->type === self::TYPE_INITIAL;
    }

    public function isSecondInspection(): bool
    {
        return $this->type === self::TYPE_SECOND;
    }

    public function isFinalInspection(): bool
    {
        return $this->type === self::TYPE_FINAL;
    }

    public function hasCriticalFailures(): bool
    {
        return !empty($this->critical_failed_items);
    }

    public function canProceedToNextStage(): bool
    {
        // Initial inspection: can proceed if no critical failures in items not in second/final
        if ($this->isInitialInspection()) {
            if (empty($this->critical_failed_items)) {
                return true;
            }
            
            foreach ($this->critical_failed_items as $criticalItem) {
                if (!($criticalItem['included_in_second_final'] ?? false)) {
                    return false;
                }
            }
            return true;
        }

        // Second/Final/Compliance: can only proceed if passed
        return $this->isPassed();
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
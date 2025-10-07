<?php
// app/Models/Application.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'application_number',
        'user_id',
        'consultant_id',
        
        // Educator Information
        'educator_first_name',
        'educator_last_name',
        'email',
        'phone',
        
        // Dayhome Address
        'address_line_1',
        'city',
        'province',
        'postal_code',
        
        // Professional Details
        'childcare_level',
        'referred_by',
        'has_criminal_record_check',
        'has_first_aid_cpr',
        'languages_spoken',
        'childcare_education',
        
        // Home Details
        'home_residents_count',
        'home_residents_details',
        'smoking_status',
        'smoking_details',
        'has_pets',
        'pets_details',
        
        // Current Dayhome Operation
        'currently_operating',
        'current_operation_details',
        'evening_overnight_care',
        'home_type',
        'home_ownership',
        
        // Goals and Philosophy
        'desired_start_date',
        'comfortable_special_needs',
        'motivation',
        'why_spiced',
        'education_philosophy',
        'fenced_backyard',
        'program_planning_process',
        
        // Status & Workflow
        'status',
        'current_stage',
        'completion_percentage',
        'requirements_checklist',
        'admin_notes',
        'rejection_reason',
        'submitted_at',
        'approved_at',
        'license_expires_at',
        'certificate_number',
    ];

    protected $casts = [
        'has_criminal_record_check' => 'boolean',
        'has_first_aid_cpr' => 'boolean',
        'has_pets' => 'boolean',
        'currently_operating' => 'boolean',
        'evening_overnight_care' => 'boolean',
        'comfortable_special_needs' => 'boolean',
        'fenced_backyard' => 'boolean',
        'completion_percentage' => 'decimal:2',
        'requirements_checklist' => 'json',
        'desired_start_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'license_expires_at' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function consultant()
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }

    public function stages()
    {
        return $this->hasMany(ApplicationStage::class)->orderBy('sort_order');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['approved', 'rejected', 'cancelled']);
    }

    public function scopeForConsultant($query, $consultantId)
    {
        return $query->where('consultant_id', $consultantId);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', '!=', 'draft');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return trim("{$this->educator_first_name} {$this->educator_last_name}");
    }

    public function getFullAddressAttribute()
    {
        return implode(', ', array_filter([
            $this->address_line_1,
            $this->city,
            $this->province,
            $this->postal_code
        ]));
    }

    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            'draft' => 'gray',
            'submitted', 'documents_submitted' => 'blue',
            'phone_interview_scheduled', 'meet_and_greet_scheduled', 
            'initial_inspection_scheduled', 'second_inspection_scheduled',
            'contract_signing_scheduled' => 'yellow',
            'approved', 'contract_signed' => 'green',
            'rejected', 'cancelled' => 'red',
            default => 'blue'
        };
    }

    public function getStatusDisplayAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getCurrentStageDisplayAttribute()
    {
        return match($this->current_stage) {
            'intake' => 'Application Intake',
            'phone_interview' => 'Phone Interview',
            'meet_and_greet' => 'Meet & Greet',
            'initial_inspection' => 'Initial Inspection',
            'document_collection' => 'Document Collection',
            'second_inspection' => 'Second Inspection',
            'contract_signing' => 'Contract Signing',
            'approved' => 'Approved & Licensed',
            default => ucwords(str_replace('_', ' ', $this->current_stage))
        };
    }

    public function getRequiredDocumentsForStage()
{
    // Return required documents based on current stage
    $requirements = [
        'intake' => [],
        'phone_interview' => [],
        'meet_and_greet' => [],
        'initial_inspection' => [
            'Criminal Record Check',
            'First Aid & CPR Certificate',
        ],
        'document_collection' => [
            'Criminal Record Check',
            'First Aid & CPR Certificate',
            'Home Insurance',
            'Vehicle Insurance',
            'Educational Certificates',
        ],
        'second_inspection' => [],
        'contract_signing' => [],
        'approved' => [],
    ];

    return $requirements[$this->current_stage] ?? [];
}

    // Helper methods
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isActive()
    {
        return !in_array($this->status, ['approved', 'rejected', 'cancelled']);
    }

    public function canBeEdited()
    {
        return in_array($this->status, ['draft', 'documents_pending']);
    }

    public function canBeSubmitted()
    {
        return $this->status === 'draft' && $this->completion_percentage >= 100;
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isSubmitted()
    {
        return !in_array($this->status, ['draft', 'cancelled']);
    }

    public function calculateCompletionPercentage()
    {
        $fields = [
            'educator_first_name', 'educator_last_name', 'email', 'phone',
            'address_line_1', 'city', 'province', 'postal_code',
            'childcare_level', 'languages_spoken', 'childcare_education',
            'home_residents_count', 'smoking_status',
            'home_type', 'home_ownership', 'desired_start_date',
            'motivation', 'why_spiced', 'education_philosophy',
            'program_planning_process'
        ];

        $completed = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $completed++;
            }
        }

        // Add boolean fields
        $booleanFields = [
            'has_criminal_record_check', 'has_first_aid_cpr',
            'comfortable_special_needs', 'fenced_backyard'
        ];
        foreach ($booleanFields as $field) {
            if ($this->$field !== null) {
                $completed++;
            }
        }

        $total = count($fields) + count($booleanFields);
        return round(($completed / $total) * 100, 2);
    }

    public function updateCompletionPercentage()
    {
        $this->update([
            'completion_percentage' => $this->calculateCompletionPercentage()
        ]);
    }

    public function moveToNextStage()
    {
        $stages = [
            'intake' => 'phone_interview',
            'phone_interview' => 'meet_and_greet',
            'meet_and_greet' => 'initial_inspection',
            'initial_inspection' => 'document_collection',
            'document_collection' => 'second_inspection',
            'second_inspection' => 'contract_signing',
            'contract_signing' => 'approved',
        ];

        if (isset($stages[$this->current_stage])) {
            $this->update(['current_stage' => $stages[$this->current_stage]]);
        }
    }

   public static function generateApplicationNumber($postalCode)
{
    // Get last 2 digits of current year
    $year = date('y');
    
    // Remove spaces from postal code and convert to uppercase
    $postalPrefix = strtoupper(str_replace(' ', '', $postalCode));
    
    // Create the prefix: SPC-{YY}{PostalCode}
    $prefix = "SPC-{$year}{$postalPrefix}";
    
    // Get the last application number with this prefix
    $lastNumber = static::where('application_number', 'LIKE', "{$prefix}-%")
        ->orderBy('application_number', 'desc')
        ->value('application_number');
    
    if ($lastNumber) {
        // Extract the sequence number from the last application
        $sequence = intval(substr($lastNumber, -4)) + 1;
    } else {
        // Start from 1 if no previous applications exist
        $sequence = 1;
    }
    
    // Format: SPC-{YY}{PostalCode}-{0000}
    return $prefix . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
}


protected static function boot()
{
    parent::boot();

    static::creating(function ($application) {
        // Get postal code from the application or user
        $postalCode = $application->postal_code ?? $application->user->postal_code ?? '000';
        
        $application->application_number = static::generateApplicationNumber($postalCode);
        
        // Auto-fill from user if not provided
        if (empty($application->educator_first_name)) {
            $nameParts = explode(' ', $application->user->name);
            $application->educator_first_name = $nameParts[0];
            $application->educator_last_name = $nameParts[1] ?? '';
        }
        if (empty($application->email)) {
            $application->email = $application->user->email;
        }
    });
}
}
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
            'anonymous_token',
            'registration_token',
            'registration_token_expires_at',
            'account_created', 
            'account_created_at',
                    
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
            'activated_at',
            'next_compliance_inspection_due',
            'last_compliance_inspection_at',
            'suspended_at',
            'terminated_at',
            'remediation_deadline',
            'remediation_notes',
            'imported_by_consultant',
            'imported_at',
            'legacy_import',
            'workflow_concluded',
            'synced_at',
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
            'account_created' => 'boolean',
            'account_created_at' => 'datetime',
            'activated_at' => 'datetime',
            'next_compliance_inspection_due' => 'date',
            'last_compliance_inspection_at' => 'datetime',
            'suspended_at' => 'datetime',
            'terminated_at' => 'datetime',
            'remediation_deadline' => 'date',
            'registration_token_expires_at' => 'datetime',
            'imported_at' => 'datetime',
            'legacy_import' => 'boolean',
            'workflow_concluded' => 'boolean',
            'synced_at' => 'datetime',
        ];

        // Relations for imported info
        public function importedByConsultant()
        {
            return $this->belongsTo(User::class, 'imported_by_consultant');
        }

        protected $appends = [
            'full_name',
            'full_address',
            'status_badge_color',
            'status_display',
            'current_stage_display',
            'application_progress_percentage', 
            'document_completion_percentage', 
            'combined_progress', 
            'current_stage_name',  
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
        public function isActiveDayhome(): bool
        {
            return $this->status === 'active';
        }

        public function isSuspended(): bool
        {
            return $this->status === 'suspended';
        }

        public function isTerminated(): bool
        {
            return $this->status === 'terminated';
        }
        /**
         * Check if application is anonymous (no user account yet)
       */
        public function isAnonymous(): bool
        {
            return is_null($this->user_id) && !is_null($this->anonymous_token);
        }
        
        /**
         * Certificate relationship
         */
        public function certificate()
        {
            return $this->hasOne(Certificate::class)->latest();
        }
        
        /**
         * All certificates (including revoked/expired)
         */
        public function certificates()
        {
            return $this->hasMany(Certificate::class);
        }
        
        /**
         * Get active certificate
         */
        public function activeCertificate()
        {
            return $this->hasOne(Certificate::class)->where('status', 'active');
        }

/**
 * Check if application has valid certificate
 */
public function hasValidCertificate()
{
    return $this->certificate && $this->certificate->isValid();
}

        /**
         * Check if application can be linked to a user account
         */
       /**
 * Check if applicant can create account
 */
        public function canCreateAccount()
        {
             if ($this->imported_at !== null) {
              return is_null($this->user_id);
         }
            return in_array($this->status, [
                'initial_inspection_completed',
                'documents_pending',
                'documents_submitted',
                'documents_approved',
                'second_inspection_scheduled',
                'second_inspection_completed',
                'final_inspection_scheduled',
                'final_inspection_completed',
                'contract_signing_scheduled',
                'contract_signed',

            ]) && is_null($this->user_id);
        }

        public function isDueForComplianceInspection(): bool
        {
            return $this->isActiveDayhome() && 
                $this->next_compliance_inspection_due && 
                $this->next_compliance_inspection_due <= now();
        }

        public function daysUntilNextInspection(): ?int
        {
            if (!$this->next_compliance_inspection_due) {
                return null;
            }

            return now()->diffInDays($this->next_compliance_inspection_due, false);
        }


        public function documentRequirements()
    {
        return $this->belongsToMany(DocumentRequirement::class, 'application_document_requirement')
                    ->withPivot('is_required')
                    ->withTimestamps();
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

        public function scopeActive($query){
                return $query->whereIn('status', [
                    'submitted',
                    'under_review',
                    'meet_and_greet_scheduled',
                    'meet_and_greet_completed',
                    'initial_inspection_scheduled',
                    'initial_inspection_completed',
                    'documents_pending',
                    'documents_submitted',
                    'documents_approved',
                    'second_inspection_scheduled',
                    'second_inspection_completed',
                    'final_inspection_scheduled',
                    'final_inspection_completed',
                    'contract_signing_scheduled',
                    'contract_signed',
                    'approved',
                    'active',
                    'compliance_inspection_due',
                    'compliance_inspection_scheduled',
                ]);
            }

            public function scopeForConsultant($query, $consultantId)
            {
                return $query->where('consultant_id', $consultantId);
            }

            public function scopeSubmitted($query)
            {
                return $query->where('status', '!=', 'draft');
            }

            public function scopeActiveDayhomes($query)
            {
                return $query->where('status', 'active');
            }

            public function scopeDueForComplianceInspection($query)
            {
                return $query->where('status', 'active')
                            ->where('next_compliance_inspection_due', '<=', now());
            }
        
        /**
         * Override getFullNameAttribute to support anonymous applications
         */
        public function getFullNameAttribute()
        {
            if ($this->educator_first_name && $this->educator_last_name) {
                return trim("{$this->educator_first_name} {$this->educator_last_name}");
            }
            return 'N/A';
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
                'final_inspection_scheduled',
                'contract_signing_scheduled' => 'yellow',
                'approved', 'contract_signed' => 'green',
                'rejected', 'cancelled' => 'red',
                default => 'blue'
            };
        }
        public function getCompletionPercentageAttribute()
        {
            // Get required documents for this application
            $requiredDocs = $this->getRequiredDocumentsForStage();
            
            // Check if there are any required documents
            if ($requiredDocs->isEmpty()) {
                return 0; // No requirements = 0% (or you could return 100 if you prefer)
            }
            
            // Get the IDs of required document requirements
            $requiredDocIds = $requiredDocs->pluck('id')->toArray();
            
            // Count uploaded documents that match the required document requirements
            $uploadedDocs = $this->documents()
                ->whereIn('document_requirement_id', $requiredDocIds)
                ->where('status', '!=', 'rejected')
                ->count();
            
            // Calculate percentage
            return round(($uploadedDocs / $requiredDocs->count()) * 100, 2);
        }

       /**
         * Override getEmailAttribute to support anonymous applications
        */
        public function getEmailAttribute($value)
        {
            // If email is directly on the application record, return it
            if (isset($this->attributes['email']) && !empty($this->attributes['email'])) {
                return $this->attributes['email'];
            }
            
            // Otherwise try to get from user relationship
            if ($this->user) {
                return $this->user->email;
            }
            
            return null;
        }

      
        /**
         * Override getPhoneAttribute to support anonymous applications
         */
        public function getPhoneAttribute($value)
        {
            // If phone is directly on the application record, return it
            if (isset($this->attributes['phone']) && !empty($this->attributes['phone'])) {
                return $this->attributes['phone'];
            }
            
            // Otherwise try to get from user relationship
            if ($this->user) {
                return $this->user->phone;
            }
            
            return 'Not specified';
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
                'final_inspection' => 'Final Inspection',
                'contract_signing' => 'Contract Signing',
                'approved' => 'Approved & Licensed',
                'active' => 'active',
                default => ucwords(str_replace('_', ' ', $this->current_stage))
            };
        }

      public function getRequiredDocumentsForStage($stage = null)
    {
    $stage = $stage ?? $this->current_stage;

    // Get application-specific requirements (assigned by consultant)
    $requiredDocuments = $this->documentRequirements()
        ->wherePivot('is_required', true)
        ->where('is_active', true)
        ->get();

    // If no specific requirements assigned, fallback to default stage requirements
    if ($requiredDocuments->isEmpty()) {
        $requiredDocuments = DocumentRequirement::where('stage', $stage)
            ->where('is_required', true)
            ->where('is_active', true)
            ->get();
    }

    return $requiredDocuments;
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
            return in_array($this->status, ['draft']);
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
                'second_inspection' => 'final_inspection',
                'final_inspection' => 'contract_signing',
                'contract_signing' => 'approved',
                'approved' => 'active',
            ];

            if (isset($stages[$this->current_stage])) {
                $this->update(['current_stage' => $stages[$this->current_stage]]);
            }
        }

     /**
     * Static method to generate application number (for both authenticated and anonymous)
     */
    public static function generateApplicationNumber($postalCode = null)
    {
        // Get last 2 digits of current year
        $year = date('y');
        
        // Use postal code if provided, otherwise use default
        $postalCode = $postalCode ?? '000';
        
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

        /**
     * Calculate overall application progress based on status
     * This is different from form completion percentage
     */
    public function getApplicationProgressPercentageAttribute()
    {
        $statusProgressMap = [
            'draft' => 5,
            'submitted' => 10,
            'meet_and_greet_scheduled' => 20,
            'meet_and_greet_completed' => 25,
            'initial_inspection_scheduled' => 30,
            'initial_inspection_completed' => 40,
            'documents_pending' => 45,
            'documents_submitted' => 55,
            'documents_approved' => 65,
            'second_inspection_scheduled' => 70,
            'second_inspection_completed' => 75,
            'final_inspection_scheduled' => 80,
            'final_inspection_completed' => 85,
            'final_inspection_passed' => 87,
            'final_inspection_failed' => 85, // Failed, but still in process
            'contract_signing_scheduled' => 90,
            'contract_signed' => 95,
            'approved' => 98,
            'active' => 100,
            // Post-activation statuses remain at 100%
            'compliance_inspection_due' => 100,
            'compliance_inspection_scheduled' => 100,
            'compliance_inspection_completed' => 100,
            'suspended' => 100,
            'under_review' => 100,
            'remediation_required' => 100,
            'terminated' => 100,
            'rejected' => 0,
            'cancelled' => 0,
        ];

        return $statusProgressMap[$this->status] ?? 0;
    }

    
    /**
     * Calculate form completion percentage (existing method - updated)
     * This measures how complete the application form data is
     */
    public function calculateCompletionPercentage()
    {
        $requiredFields = [
            'educator_first_name', 
            'educator_last_name', 
            'email', 
            'phone',
            'address_line_1', 
            'city', 
            'province', 
            'postal_code',
            'childcare_level', 
            'languages_spoken', 
            'childcare_education',
            'home_residents_count', 
            'smoking_status',
            'home_type', 
            'home_ownership', 
            'desired_start_date',
            'motivation', 
            'why_spiced', 
            'education_philosophy',
            'program_planning_process'
        ];

        $completed = 0;
        foreach ($requiredFields as $field) {
            if (!empty($this->$field)) {
                $completed++;
            }
        }

        // Add boolean fields
        $booleanFields = [
            'has_criminal_record_check', 
            'has_first_aid_cpr',
            'comfortable_special_needs', 
            'fenced_backyard'
        ];
        
        foreach ($booleanFields as $field) {
            if ($this->$field !== null) {
                $completed++;
            }
        }

        $total = count($requiredFields) + count($booleanFields);
        return round(($completed / $total) * 100, 2);
    }

    /**
     * Get document completion percentage for current stage
     */
    public function getDocumentCompletionPercentageAttribute()
    {
        $requiredDocs = $this->getRequiredDocumentsForStage();
        
        if ($requiredDocs->isEmpty()) {
            return 100; // No documents required = 100%
        }
        
        $requiredDocIds = $requiredDocs->pluck('id')->toArray();
        
        $uploadedDocs = $this->documents()
            ->whereIn('document_requirement_id', $requiredDocIds)
            ->where('status', '!=', 'rejected')
            ->count();
        
        return round(($uploadedDocs / $requiredDocs->count()) * 100, 2);
    }

    /**
     * Get combined progress considering both form and documents
     */
    public function getCombinedProgressAttribute()
    {
        // If still in draft, only consider form completion
        if ($this->status === 'draft') {
            return $this->completion_percentage;
        }
        
        // For document stages, consider both form and documents
        if (in_array($this->status, ['documents_pending', 'documents_submitted'])) {
            $formProgress = $this->completion_percentage;
            $docProgress = $this->document_completion_percentage;
            
            // Weight: 70% form, 30% documents
            return round(($formProgress * 0.7) + ($docProgress * 0.3), 2);
        }
        
        // Otherwise use status-based progress
        return $this->application_progress_percentage;
    }

    /**
     * Get current stage display name
     */
    public function getCurrentStageNameAttribute()
    {
        $stageMap = [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'meet_and_greet_scheduled' => 'Meet & Greet',
            'meet_and_greet_completed' => 'Meet & Greet',
            'initial_inspection_scheduled' => 'Initial Inspection',
            'initial_inspection_completed' => 'Initial Inspection',
            'documents_pending' => 'Document Submission',
            'documents_submitted' => 'Document Submission',
            'documents_approved' => 'Document Submission',
            'second_inspection_scheduled' => 'Second Inspection',
            'second_inspection_completed' => 'Second Inspection',
            'final_inspection_scheduled' => 'Final Inspection',
            'final_inspection_completed' => 'Final Inspection',
            'final_inspection_passed' => 'Final Inspection',
            'final_inspection_failed' => 'Final Inspection',
            'contract_signing_scheduled' => 'Contract Signing',
            'contract_signed' => 'Contract Signing',
            'approved' => 'Approved',
            'active' => 'Active',
            'compliance_inspection_due' => 'Compliance Monitoring',
            'compliance_inspection_scheduled' => 'Compliance Monitoring',
            'compliance_inspection_completed' => 'Compliance Monitoring',
            'suspended' => 'Suspended',
            'under_review' => 'Under Review',
            'remediation_required' => 'Remediation Required',
            'terminated' => 'Terminated',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
        ];

        return $stageMap[$this->status] ?? 'Unknown';
    }

    /**
     * Check if application is in post-activation phase
     */
    public function isInPostActivationPhase()
    {
        return in_array($this->status, [
            'active',
            'compliance_inspection_due',
            'compliance_inspection_scheduled',
            'compliance_inspection_completed',
            'suspended',
            'under_review',
            'remediation_required',
        ]);
    }

    /**
     * Check if application is in terminal state
     */
    public function isInTerminalState()
    {
        return in_array($this->status, ['rejected', 'cancelled', 'terminated']);
    }

        /**
     * Update boot method to handle anonymous applications
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($application) {
            // Generate application number if not set
            if (empty($application->application_number)) {
                $postalCode = $application->postal_code ?? '000';
                $application->application_number = static::generateApplicationNumber($postalCode);
            }
            
            // Only auto-fill from user if user exists and fields are empty
            if ($application->user_id && $application->user) {
                if (empty($application->educator_first_name)) {
                    $nameParts = explode(' ', $application->user->name);
                    $application->educator_first_name = $nameParts[0];
                    $application->educator_last_name = $nameParts[1] ?? '';
                }
                if (empty($application->email)) {
                    $application->email = $application->user->email;
                }
                if (empty($application->phone)) {
                    $application->phone = $application->user->phone;
                }
            }
        });
    }
}
<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'user_type',
        'is_active',
        'last_login_at',
        'preferences',
        'avatar',
        'email_verification_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'preferences' => 'json',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Relationships

    /**
     * Applications created by this user (for applicants)
     */
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Applications assigned to this user (for consultants)
     */
    public function assignedApplications()
    {
        return $this->hasMany(Application::class, 'consultant_id');
    }

    /**
     * Appointments for this user (applicants)
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'applicant_id');
    }

    /**
     * Appointments assigned to this user (consultants)
     */
    public function consultantAppointments()
    {
        return $this->hasMany(Appointment::class, 'consultant_id');
    }

    /**
     * Documents uploaded by this user
     */
    public function uploadedDocuments()
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    /**
     * Documents reviewed by this user (consultants)
     */
    public function reviewedDocuments()
    {
        return $this->hasMany(Document::class, 'reviewed_by');
    }

    /**
     * Inspections conducted by this user (consultants)
     */
    public function inspections()
    {
        return $this->hasMany(Inspection::class, 'consultant_id');
    }

    /**
     * Notifications for this user
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Consultant profile (if user is a consultant)
     */
    public function consultant()
    {
        return $this->hasOne(Consultant::class);
    }

    /**
     * Application stages this user has worked on
     */
    public function applicationStages()
    {
        return $this->hasMany(ApplicationStage::class);
    }

    /**
     * Audit logs for this user's actions
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('user_type', $type);
    }

    public function scopeApplicants($query)
    {
        return $query->where('user_type', 'applicant');
    }

    public function scopeConsultants($query)
    {
        return $query->where('user_type', 'consultant');
    }

    public function scopeAdmins($query)
    {
        return $query->where('user_type', 'admin');
    }

    // Accessors & Mutators

    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->province,
            $this->postal_code
        ]);

        return implode(', ', $parts);
    }

    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        $initials = '';
        
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        
        return substr($initials, 0, 2);
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Default avatar based on initials
        return "https://ui-avatars.com/api/?name={$this->initials}&color=7C3AED&background=EDE9FE&bold=true";
    }

    // Helper Methods

    public function isApplicant()
    {
        return $this->user_type === 'applicant';
    }

    public function isConsultant()
    {
        return $this->user_type === 'consultant';
    }

    public function isAdmin()
    {
        return $this->user_type === 'admin';
    }

    public function hasActiveApplication()
    {
        return $this->applications()->whereIn('status', [
            'draft', 'submitted', 'under_review', 'initial_inspection_scheduled',
            'initial_inspection_completed', 'documents_pending', 'documents_submitted', 'documents_approved',
            'second_inspection_scheduled', 'second_inspection_completed',
            'final_review'
        ])->exists();
    }

    public function getActiveApplication()
    {
        return $this->applications()->whereIn('status', [
            'draft', 'submitted', 'meet_and_greet_scheduled','meet_and_greet_completed', 'initial_inspection_scheduled',
            'initial_inspection_completed', 'documents_pending', 'documents_submitted', 'documents_approved',
            'second_inspection_scheduled', 'second_inspection_completed',
            'final_review'
        ])->latest()->first();
    }

    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }

    public function getUnreadNotificationsCount()
    {
        return $this->notifications()->where('is_read', false)->count();
    }

    public function canAccessApplication(Application $application)
    {
        // Applicants can only access their own applications
        if ($this->isApplicant()) {
            return $this->id === $application->user_id;
        }

        // Consultants can access assigned applications
        if ($this->isConsultant()) {
            return $this->id === $application->consultant_id || 
                   $this->consultant?->can_view_all_applications;
        }

        // Admins can access all applications
        return $this->isAdmin();
    }

    /**
     * Get user's dashboard route based on role
     */
    public function getDashboardRoute()
    {
        return match($this->user_type) {
            'applicant' => route('applicant.dashboard'),
            'consultant' => route('consultant.dashboard'),
            'admin' => route('admin.dashboard'),
            default => route('dashboard'),
        };
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Set default preferences
            if (empty($user->preferences)) {
                $user->preferences = [
                    'theme' => 'light',
                    'notifications' => [
                        'email' => true,
                        'browser' => true,
                        'sms' => false
                    ],
                    'language' => 'en'
                ];
            }
        });

        static::created(function ($user) {
            // Assign default role based on user_type
            $roleName = $user->user_type;
            if (\Spatie\Permission\Models\Role::where('name', $roleName)->exists()) {
                $user->assignRole($roleName);
            }
        });
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EducatorProfile extends Model
{
    use HasFactory, SoftDeletes;

       protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'date_of_hire',
        'sin_number',
        'date_of_birth',
        'marital_status',
        'religious_beliefs',
        'ethnicity_nationality',
        'allergies',
        'dietary_restrictions',
        'medical_conditions',
        'activity_restrictions',
        'emergency_contact_1_first_name',
        'emergency_contact_1_last_name',
        'emergency_contact_1_relationship',
        'emergency_contact_1_phone',
        'emergency_contact_1_address_line_1',
        'emergency_contact_1_city',
        'emergency_contact_1_province',
        'emergency_contact_1_postal_code',
        'emergency_contact_2_first_name',
        'emergency_contact_2_last_name',
        'emergency_contact_2_relationship',
        'emergency_contact_2_phone',
        'emergency_contact_2_address_line_1',
        'emergency_contact_2_city',
        'emergency_contact_2_province',
        'emergency_contact_2_postal_code',
        'professional_bio',
        'operating_hours_start',
        'operating_hours_end',
        'current_capacity',
        'maximum_capacity',
        'specializations',
        'professional_goals',
        'profile_photo',
        'is_complete',
        'last_updated_at',
    ];

        protected $casts = [
        'specializations' => 'array',
        'operating_hours_start' => 'datetime',
        'operating_hours_end' => 'datetime',
        'date_of_hire' => 'date',
        'date_of_birth' => 'date',
        'current_capacity' => 'integer',
        'maximum_capacity' => 'integer',
        'is_complete' => 'boolean',
        'last_updated_at' => 'datetime',
    ];
    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(EducatorProfileItem::class)->orderBy('sort_order');
    }

    public function applicationDocuments()
{
    return $this->hasMany(Document::class, 'application_id', 'user_id')
        ->whereHas('application', function($query) {
            $query->where('user_id', $this->user_id);
        })
        ->latest();
}

// Get documents grouped by application
public function getDocumentsByApplication()
{
    return Document::whereHas('application', function($query) {
        $query->where('user_id', $this->user_id);
    })
    ->with(['application', 'documentRequirement'])
    ->latest()
    ->get()
    ->groupBy('application_id');
}
    public function activeItems()
    {
        return $this->hasMany(EducatorProfileItem::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function documents()
    {
        return $this->hasMany(EducatorProfileItem::class)
            ->where('type', 'document')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    /**
     * Get expiring items (within 30 days)
     */
    public function expiringItems()
    {
        return $this->hasMany(EducatorProfileItem::class)
            ->where('is_active', true)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays(30))
            ->whereDate('expiry_date', '>=', now())
            ->orderBy('expiry_date');
    }

    /**
     * Get expired items
     */
    public function expiredItems()
    {
        return $this->hasMany(EducatorProfileItem::class)
            ->where('is_active', true)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', now())
            ->orderBy('expiry_date', 'desc');
    }

    /**
     * Check if profile is complete
     */
    public function checkCompleteness()
    {
        $hasBasicInfo = !empty($this->professional_bio) 
            && !empty($this->operating_hours_start)
            && !empty($this->operating_hours_end)
            && !empty($this->maximum_capacity);

        $hasItems = $this->activeItems()->count() > 0;

        $isComplete = $hasBasicInfo && $hasItems;

        if ($this->is_complete !== $isComplete) {
            $this->update([
                'is_complete' => $isComplete,
                'last_updated_at' => now()
            ]);
        }

        return $isComplete;
    }

    /**
     * Get completion percentage
     */
    public function getCompletionPercentageAttribute()
    {
        $fields = [
            'professional_bio',
            'operating_hours_start',
            'operating_hours_end',
            'maximum_capacity',
            'specializations',
        ];

        $completed = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $completed++;
            }
        }

        $basePercentage = ($completed / count($fields)) * 70; // 70% for core fields
        $itemsPercentage = $this->activeItems()->count() > 0 ? 30 : 0; // 30% for having items

        return round($basePercentage + $itemsPercentage, 2);
    }

    /**
     * Get available capacity
     */
    public function getAvailableCapacityAttribute()
    {
        if (!$this->maximum_capacity) {
            return 0;
        }

        return max(0, $this->maximum_capacity - $this->current_capacity);
    }

    /**
     * Scopes
     */
    public function scopeComplete($query)
    {
        return $query->where('is_complete', true);
    }

    public function scopeIncomplete($query)
    {
        return $query->where('is_complete', false);
    }
}
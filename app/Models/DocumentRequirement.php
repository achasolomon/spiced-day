<?php

// app/Models/DocumentRequirement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'instructions',
        'category',
        'stage',
        'is_required',
        'is_conditional',
        'conditions',
        'accepted_formats',
        'max_file_size',
        'max_files',
        'has_expiry',
        'validity_period',
        'requires_annual_renewal',
        'requires_review',
        'review_priority',
        'review_criteria',
        'rejection_reasons',
        'sort_order',
        'is_active',
        'icon',
        'help_text',
        'example_url',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_conditional' => 'boolean',
        'conditions' => 'json',
        'accepted_formats' => 'json',
        'has_expiry' => 'boolean',
        'requires_annual_renewal' => 'boolean',
        'requires_review' => 'boolean',
        'review_criteria' => 'json',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeByStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Helper methods
    public function isApplicableFor($conditions = [])
    {
        if (!$this->is_conditional) {
            return true;
        }

        // Implement your conditional logic here
        return true;
    }

    public function getAcceptedFormatsString()
    {
        if (!$this->accepted_formats) {
            return 'Any format';
        }

        return implode(', ', $this->accepted_formats);
    }

    public function getMaxFileSizeHuman()
    {
        if (!$this->max_file_size) {
            return 'No limit';
        }

        $bytes = $this->max_file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes >= 1024 && $i < 3; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
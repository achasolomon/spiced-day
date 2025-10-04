<?php

// app/Models/InspectionItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'checklist_id',
        'code',
        'title',
        'description',
        'criteria',
        'instructions',
        'category',
        'subcategory',
        'response_type',
        'response_options',
        'requires_photo',
        'requires_comment',
        'weight',
        'is_critical',
        'is_mandatory',
        'points_possible',
        'applicable_when',
        'not_applicable_when',
        'help_text',
        'reference_documents',
        'regulation_reference',
        'section',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'response_options' => 'json',
        'requires_photo' => 'boolean',
        'requires_comment' => 'boolean',
        'is_critical' => 'boolean',
        'is_mandatory' => 'boolean',
        'points_possible' => 'decimal:2',
        'applicable_when' => 'json',
        'not_applicable_when' => 'json',
        'reference_documents' => 'json',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function checklist()
    {
        return $this->belongsTo(InspectionChecklist::class, 'checklist_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCritical($query)
    {
        return $query->where('is_critical', true);
    }

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Helper methods
    public function isApplicable($conditions = [])
    {
        // Implement your conditional logic here
        return true;
    }

    public function getResponseOptionsFormatted()
    {
        if ($this->response_type === 'yes_no') {
            return ['Yes', 'No', 'N/A'];
        }

        if ($this->response_options) {
            return $this->response_options;
        }

        return [];
    }
}

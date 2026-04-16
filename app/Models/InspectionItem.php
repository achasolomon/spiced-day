<?php

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
        'is_critical_initial',
        'is_critical_second',
        'is_critical_final',
        'is_critical_compliance',
        'is_mandatory',
        'points_possible',
        'included_in_initial',
        'included_in_second',
        'included_in_final',
        'included_in_compliance',
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
        'is_critical_initial' => 'boolean',
        'is_critical_second' => 'boolean',
        'is_critical_final' => 'boolean',
        'is_critical_compliance' => 'boolean',
        'is_mandatory' => 'boolean',
        'included_in_initial' => 'boolean',
        'included_in_second' => 'boolean',
        'included_in_final' => 'boolean',
        'included_in_compliance' => 'boolean',
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

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeForInspectionType($query, $inspectionType)
    {
        switch ($inspectionType) {
            case 'initial_inspection':
                return $query->where('included_in_initial', true);
            case 'second_inspection':
                return $query->where('included_in_second', true);
            case 'final_inspection':
                return $query->where('included_in_final', true);
            case 'compliance_inspection_scheduled':
            case 'compliance_inspection_unscheduled':
                return $query->where('included_in_compliance', true);
            default:
                return $query;
        }
    }

    public function scopeCriticalForType($query, $inspectionType)
    {
        switch ($inspectionType) {
            case 'initial_inspection':
                return $query->where('is_critical_initial', true);
            case 'second_inspection':
                return $query->where('is_critical_second', true);
            case 'final_inspection':
                return $query->where('is_critical_final', true);
            case 'compliance_inspection_scheduled':
            case 'compliance_inspection_unscheduled':
                return $query->where('is_critical_compliance', true);
            default:
                return $query;
        }
    }

    // Helper methods
    public function isCriticalForInspectionType($inspectionType)
    {
        switch ($inspectionType) {
            case 'initial_inspection':
                return $this->is_critical_initial;
            case 'second_inspection':
                return $this->is_critical_second;
            case 'final_inspection':
                return $this->is_critical_final;
            case 'compliance_inspection_scheduled':
            case 'compliance_inspection_unscheduled':
                return $this->is_critical_compliance;
            default:
                return false;
        }
    }

    public function isIncludedInInspectionType($inspectionType)
    {
        switch ($inspectionType) {
            case 'initial_inspection':
                return $this->included_in_initial;
            case 'second_inspection':
                return $this->included_in_second;
            case 'final_inspection':
                return $this->included_in_final;
            case 'compliance_inspection_scheduled':
            case 'compliance_inspection_unscheduled':
                return $this->included_in_compliance;
            default:
                return false;
        }
    }

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
<?php
// app/Models/InspectionChecklist.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'version',
        'inspection_type',
        'dayhome_type',
        'is_active',
        'is_default',
        'total_items',
        'scoring_system',
        'passing_score',
        'instructions',
        'required_materials',
        'estimated_duration',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'scoring_system' => 'json',
        'passing_score' => 'decimal:2',
        'required_materials' => 'json',
    ];

    // Relationships
    public function items()
    {
        return $this->hasMany(InspectionItem::class, 'checklist_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeForInspectionType($query, $type)
    {
        return $query->where('inspection_type', $type);
    }

    public function scopeForDayhomeType($query, $type)
    {
        return $query->where('dayhome_type', $type)
                    ->orWhere('dayhome_type', 'all');
    }

    // Helper methods
    public function updateTotalItems()
    {
        $this->update(['total_items' => $this->items()->count()]);
    }

    public function getItemsByCategory()
    {
        return $this->items()
                   ->orderBy('sort_order')
                   ->get()
                   ->groupBy('category');
    }
}

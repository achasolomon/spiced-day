<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'sort_order',
        'is_active',
        'requires_expiry',
        'default_validity_days',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_expiry' => 'boolean',
        'default_validity_days' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get all documents in this category
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get all document requirements in this category
     */
    public function documentRequirements(): HasMany
    {
        return $this->hasMany(DocumentRequirement::class);
    }

    /**
     * Scope for active categories only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered categories
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}


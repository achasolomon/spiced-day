<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class EducatorProfileItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'educator_profile_id',
        'title',
        'type',
        'value',
        'file_path',
        'file_name',
        'date_value',
        'boolean_value',
        'expiry_date',
        'notes',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'date_value' => 'date',
        'expiry_date' => 'date',
        'boolean_value' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relationships
     */
    public function educatorProfile()
    {
        return $this->belongsTo(EducatorProfile::class);
    }

    /**
     * Get the display value based on type
     */
    public function getDisplayValueAttribute()
    {
        switch ($this->type) {
            case 'boolean':
                return $this->boolean_value ? 'Yes' : 'No';
            case 'date':
                return $this->date_value ? $this->date_value->format('M d, Y') : 'N/A';
            case 'document':
                return $this->file_name ?? 'Document uploaded';
            case 'text':
            default:
                return $this->value ?? 'N/A';
        }
    }

    /**
     * Check if item is expiring soon (within 30 days)
     */
    public function getIsExpiringSoonAttribute()
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->isFuture() 
            && $this->expiry_date->diffInDays(now()) <= 30;
    }

    /**
     * Check if item is expired
     */
    public function getIsExpiredAttribute()
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->isPast();
    }

    /**
     * Get expiry status
     */
    public function getExpiryStatusAttribute()
    {
        if (!$this->expiry_date) {
            return null;
        }

        if ($this->is_expired) {
            return 'expired';
        }

        if ($this->is_expiring_soon) {
            return 'expiring';
        }

        return 'valid';
    }

    /**
     * Get file URL
     */
    public function getFileUrlAttribute()
    {
        if (!$this->file_path || !Storage::exists($this->file_path)) {
            return null;
        }

        return Storage::url($this->file_path);
    }

    /**
     * Delete file when item is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($item) {
            if ($item->file_path && Storage::exists($item->file_path)) {
                Storage::delete($item->file_path);
            }
        });
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDocuments($query)
    {
        return $query->where('type', 'document');
    }

    public function scopeExpiring($query)
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays(30))
            ->whereDate('expiry_date', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', now());
    }
}
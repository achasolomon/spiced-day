<?php
// app/Models/Document.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'application_id',
        'uploaded_by',
        'document_requirement_id',
        'name',
        'original_filename',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'file_hash',
        'category',
        'type',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'issue_date',
        'expiry_date',
        'expires',
        'validity_period',
        'version',
        'replaces_document_id',
        'is_current_version',
        'metadata',
        'description',
        'notes',
        'is_sensitive',
        'download_count',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'expires' => 'boolean',
        'is_current_version' => 'boolean',
        'metadata' => 'json',
        'is_sensitive' => 'boolean',
    ];

    // Relationships
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function documentRequirement()
    {
        return $this->belongsTo(DocumentRequirement::class);
    }

    public function replacesDocument()
    {
        return $this->belongsTo(Document::class, 'replaces_document_id');
    }

    public function replacedByDocuments()
    {
        return $this->hasMany(Document::class, 'replaces_document_id');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expires', true)
                    ->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires', true)
                    ->where('expiry_date', '<', now());
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessors
    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes >= 1024 && $i < 3; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getDownloadUrlAttribute()
    {
        return route('documents.download', $this);
    }

    // Helper methods
    public function isExpired()
    {
        return $this->expires && $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon($days = 30)
    {
        return $this->expires && 
               $this->expiry_date && 
               $this->expiry_date->isBetween(now(), now()->addDays($days));
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }
}

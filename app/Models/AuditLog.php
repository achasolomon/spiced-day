<?php

// app/Models/AuditLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'application_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'metadata',
        'ip_address',
        'user_agent',
        'session_id',
        'request_id',
        'severity',
        'category',
        'is_sensitive',
        'compliance_tags',
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        'metadata' => 'json',
        'is_sensitive' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function model()
    {
        return $this->morphTo();
    }

    // Alias for model relationship (for backward compatibility)
    public function auditable()
    {
        return $this->morphTo('auditable', 'model_type', 'model_id');
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeSensitive($query)
    {
        return $query->where('is_sensitive', true);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Helper methods
    public static function log($action, $model, $description, $additionalData = [])
    {
        static::create(array_merge([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'category' => static::determineCategory($action),
            'severity' => static::determineSeverity($action),
        ], $additionalData));
    }

    private static function determineCategory($action)
    {
        $categoryMap = [
            'login' => 'authentication',
            'logout' => 'authentication',
            'application_created' => 'application_management',
            'application_updated' => 'application_management',
            'document_uploaded' => 'document_management',
            'inspection_completed' => 'inspection',
            'appointment_scheduled' => 'appointment',
        ];

        return $categoryMap[$action] ?? 'other';
    }

    private static function determineSeverity($action)
    {
        $severityMap = [
            'login' => 'low',
            'logout' => 'low',
            'application_approved' => 'high',
            'application_rejected' => 'high',
            'document_deleted' => 'medium',
            'inspection_failed' => 'high',
        ];

        return $severityMap[$action] ?? 'low';
    }
}
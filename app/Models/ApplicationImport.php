<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'imported_by',
        'file_name',
        'file_path',
        'total_rows',
        'successful_imports',
        'failed_imports',
        'errors',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'errors' => 'array',
        'total_rows' => 'integer',
        'successful_imports' => 'integer',
        'failed_imports' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * User who imported
     */
    public function importer()
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    /**
     * Get success rate percentage
     */
    public function getSuccessRateAttribute()
    {
        if ($this->total_rows === 0) {
            return 0;
        }

        return round(($this->successful_imports / $this->total_rows) * 100, 2);
    }
}
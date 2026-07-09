<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayhomeSyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'direction',
        'endpoint',
        'http_status',
        'request_payload',
        'response_body',
        'error_message',
        'synced_at',
    ];

    protected $casts = [
        'request_payload' => 'json',
        'response_body' => 'json',
        'synced_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'certificate_number',
        'issue_date',
        'expiry_date',
        'applicant_name',
        'ceo_name',
        'ceo_signature_path',
        'pdf_path',
        'status',
        'notes',
        'issued_by',
        'revoked_at',
        'revocation_reason',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'revoked_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Check if certificate is expired
     */
    public function isExpired()
    {
        return $this->expiry_date && Carbon::parse($this->expiry_date)->isPast();
    }

    /**
     * Check if certificate is valid
     */
    public function isValid()
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    /**
     * Generate unique certificate number
     */
    public static function generateCertificateNumber()
    {
        $prefix = 'SPICED-CERT';
        $year = date('Y');
        $lastCert = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastCert ? intval(substr($lastCert->certificate_number, -4)) + 1 : 1;
        
        return sprintf('%s-%s-%04d', $prefix, $year, $number);
    }

    /**
     * Revoke certificate
     */
    public function revoke($reason = null)
    {
        $this->update([
            'status' => 'revoked',
            'revoked_at' => now(),
            'revocation_reason' => $reason,
        ]);
    }

    /**
     * Check and update expired status
     */
    public function checkExpiration()
    {
        if ($this->isExpired() && $this->status === 'active') {
            $this->update(['status' => 'expired']);
        }
    }
}
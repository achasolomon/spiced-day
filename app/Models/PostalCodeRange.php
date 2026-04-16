<?php
// app/Models/PostalCodeRange.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostalCodeRange extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id',
        'prefix',
        'full_postal_codes',
    ];

    protected $casts = [
        'full_postal_codes' => 'json',
    ];

    /**
     * The region this postal code range belongs to.
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
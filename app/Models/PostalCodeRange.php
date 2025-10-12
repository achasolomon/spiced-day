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
        'full_postal_codes' => 'json', // If using specific full codes as JSON
    ];

    /**
     * The region this postal code range belongs to.
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
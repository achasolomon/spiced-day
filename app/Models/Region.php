<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * The postal code ranges belonging to this region.
     */
    public function postalCodeRanges()
    {
        return $this->hasMany(PostalCodeRange::class);
    }

    /**
     * Alias for postalCodeRanges (for convenience)
     */
    public function postalCodes()
    {
        return $this->postalCodeRanges();
    }

    /**
     * The consultants assigned to this region.
     */
    public function consultants()
    {
        return $this->belongsToMany(Consultant::class, 'consultant_regions');
    }
}
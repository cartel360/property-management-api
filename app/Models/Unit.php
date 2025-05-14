<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'unit_number',
        'rent_amount',
        'size',
        'bedrooms',
        'bathrooms',
        'features',
        'status',
    ];

    protected $casts = [
        'features' => 'array',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function leases()
    {
        return $this->hasMany(Lease::class);
    }

    public function currentLease()
    {
        return $this->hasOne(Lease::class)->where('status', 'active')->latest();
    }
}

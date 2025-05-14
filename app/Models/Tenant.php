<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'date_of_birth',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    public function leases()
    {
        return $this->hasMany(Lease::class);
    }

    public function currentLease()
    {
        return $this->hasOne(Lease::class)->where('status', 'active')->latest();
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Lease::class);
    }
}

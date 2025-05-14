<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_id',
        'amount',
        'payment_date',
        'payment_method',
        'transaction_reference',
        'status',
        'notes',
    ];

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    public function tenant()
    {
        return $this->through('lease')->has('tenant');
    }
}

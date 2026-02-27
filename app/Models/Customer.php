<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'store_id',
        'name',
        'phone',
        'speed_package',
        'monthly_fee',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'store_id',
        'name',
        'phone',
        'address',
        'speed_package',
        'monthly_fee',
        'join_date',
        'due_date',
    ];

    protected $casts = [
        'join_date' => 'date',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}

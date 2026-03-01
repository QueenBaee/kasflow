<?php

namespace App\Models;

use Carbon\Carbon;
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
        'status',
    ];

    protected $casts = [
        'join_date' => 'date',
    ];

    protected $appends = ['payment_status', 'last_payment_date'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getLastPaymentDateAttribute()
    {
        $lastPayment = $this->transactions()
            ->where('type', 'income')
            ->latest('transaction_date')
            ->first();

        return $lastPayment ? $lastPayment->transaction_date : null;
    }

    public function getPaymentStatusAttribute()
    {
        if (!$this->due_date) {
            return 'JATUH_TEMPO';
        }

        $today = Carbon::now();
        $currentMonth = $today->month;
        $currentYear = $today->year;
        $billingDate = $this->due_date;

        // Check if there's a payment in current month
        $hasPaymentThisMonth = $this->transactions()
            ->where('type', 'income')
            ->whereYear('transaction_date', $currentYear)
            ->whereMonth('transaction_date', $currentMonth)
            ->exists();

        if ($hasPaymentThisMonth) {
            return 'LUNAS';
        }

        // Check if today has passed billing date
        if ($today->day > $billingDate) {
            return 'BELUM_BAYAR';
        }

        return 'JATUH_TEMPO';
    }

    // Query Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'AKTIF');
    }

    public function scopeLunas($query)
    {
        return $query->whereHas('transactions', function ($q) {
            $q->where('type', 'income')
                ->whereYear('transaction_date', Carbon::now()->year)
                ->whereMonth('transaction_date', Carbon::now()->month);
        });
    }

    public function scopeBelumBayar($query)
    {
        $today = Carbon::now();
        
        return $query->where('due_date', '<', $today->day)
            ->whereDoesntHave('transactions', function ($q) use ($today) {
                $q->where('type', 'income')
                    ->whereYear('transaction_date', $today->year)
                    ->whereMonth('transaction_date', $today->month);
            });
    }

    public function scopeJatuhTempo($query)
    {
        $today = Carbon::now();
        
        return $query->where('due_date', '>=', $today->day)
            ->whereDoesntHave('transactions', function ($q) use ($today) {
                $q->where('type', 'income')
                    ->whereYear('transaction_date', $today->year)
                    ->whereMonth('transaction_date', $today->month);
            });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_code', 'customer_id', 'total_amount', 
        'discount', 'final_amount', 'payment_method', 
        'status', 'transaction_date', 'notes'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($transaction) {
            if (!$transaction->transaction_code) {
                $transaction->transaction_code = 'TRX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('transaction_date', date('m'))
                    ->whereYear('transaction_date', date('Y'));
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
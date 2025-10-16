<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'address', 
        'birth_date', 'type', 'loyalty_points'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'loyalty_points' => 'integer',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(CustomerFeedback::class);
    }

    public function addLoyaltyPoints($points)
    {
        $this->increment('loyalty_points', $points);
    }
}
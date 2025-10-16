<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerFeedback extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer_feedbacks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_name',
        'email',
        'phone',
        'message',
        'rating',
        'sentiment',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'pending'
    ];

    /**
     * Get the formatted date.
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d M Y, H:i');
    }

    /**
     * Scope for positive sentiment
     */
    public function scopePositive($query)
    {
        return $query->where('sentiment', 'positive');
    }

    /**
     * Scope for negative sentiment
     */
    public function scopeNegative($query)
    {
        return $query->where('sentiment', 'negative');
    }

    /**
     * Scope for neutral sentiment
     */
    public function scopeNeutral($query)
    {
        return $query->where('sentiment', 'neutral');
    }

    /**
     * Scope for high ratings (4-5 stars)
     */
    public function scopeHighRating($query)
    {
        return $query->whereIn('rating', [4, 5]);
    }

    /**
     * Scope for low ratings (1-2 stars)
     */
    public function scopeLowRating($query)
    {
        return $query->whereIn('rating', [1, 2]);
    }
}
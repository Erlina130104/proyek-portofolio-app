<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'support_tickets';

    protected $fillable = [
        'ticket_code',
        'user_id',
        'title',
        'description',
        'priority',
        'status',
        'category',
        'assigned_to'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Auto generate ticket code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_code)) {
                $lastTicket = SupportTicket::withTrashed()->latest('id')->first();
                $number = $lastTicket ? intval(substr($lastTicket->ticket_code, 3)) + 1 : 1;
                $ticket->ticket_code = 'TCK' . str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // Helper methods untuk mapping status
    public function getFormattedStatusAttribute()
    {
        return match($this->status) {
            'open' => 'Pending',
            'in_progress' => 'In Progress',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            default => ucfirst($this->status)
        };
    }

    public function getFormattedPriorityAttribute()
    {
        return match($this->priority) {
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'High', // Map urgent ke High untuk frontend
            default => ucfirst($this->priority)
        };
    }
}
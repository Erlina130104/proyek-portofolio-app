<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_code', 'name', 'email', 'phone', 
        'position', 'department', 'salary', 'join_date', 
        'status', 'photo', 'address'
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'join_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($employee) {
            if (!$employee->employee_code) {
                $employee->employee_code = 'EMP-' . date('Y') . '-' . str_pad(Employee::count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'starts_at',
        'ends_at',
        'all_day',
        'location',
        'color',
        'created_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'all_day' => 'boolean',
    ];

    protected $attributes = [
        'color' => '#f59e0b',
        'all_day' => false,
    ];

    // Scope for events in a date range
    public function scopeInRange($query, $start, $end)
    {
        return $query->where(function ($q) use ($start, $end) {
            $q->where('starts_at', '<=', $end)
              ->where(function ($q2) use ($start) {
                  $q2->whereNull('ends_at')
                     ->orWhere('ends_at', '>=', $start);
              });
        });
    }

    // Scope for current month
    public function scopeCurrentMonth($query, $date = null)
    {
        $date = $date ?? now();
        $start = $date->copy()->startOfMonth();
        $end = $date->copy()->endOfMonth();
        return $query->inRange($start, $end);
    }

    // Scope for current week
    public function scopeCurrentWeek($query, $date = null)
    {
        $date = $date ?? now();
        $start = $date->copy()->startOfWeek();
        $end = $date->copy()->endOfWeek();
        return $query->inRange($start, $end);
    }

    // Scope for current day
    public function scopeCurrentDay($query, $date = null)
    {
        $date = $date ?? now();
        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();
        return $query->inRange($start, $end);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ResearchStudy extends Model
{
    protected $fillable = [
        'title', 'description', 'starts_at', 'ends_at',
        'application_url', 'application_cutoff_at', 'image_path',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'immutable_datetime',
            'ends_at' => 'immutable_datetime',
            'application_cutoff_at' => 'immutable_datetime',
        ];
    }

    public function scopeUpcomingOrOngoing(Builder $query): Builder
    {
        return $query->where('ends_at', '>=', now())->orderBy('starts_at');
    }

    public function applicationsClosed(): bool
    {
        $now = now();

        return $now->gte($this->application_cutoff_at)
            || $now->gt($this->starts_at->subHours(24));
    }
}

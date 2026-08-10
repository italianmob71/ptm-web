<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_initial',
        'bio',
        'image',
        'active',
        'team_member',
        'priority',
        'social_links',
    ];

    protected $casts = [
        'active' => 'boolean',
        'team_member' => 'boolean',
        'priority' => 'integer',
        'social_links' => 'array',
    ];

    protected $attributes = [
        'active' => true,
        'team_member' => false,
        'priority' => 0,
    ];

    // Accessor for full name
    public function getFullNameAttribute(): string
    {
        $middle = $this->middle_initial ? " {$this->middle_initial}." : '';
        return "{$this->first_name}{$middle} {$this->last_name}";
    }

    // Scope for active authors
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // Scope for team members
    public function scopeTeamMembers($query)
    {
        return $query->where('team_member', true);
    }

    // Scope ordered by priority
    public function scopeOrdered($query)
    {
        return $query->orderBy('priority')->orderBy('last_name')->orderBy('first_name');
    }
}
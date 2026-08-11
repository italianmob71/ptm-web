<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'security_group', 'force_update'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'security_group' => 'integer',
            'force_update' => 'boolean',
        ];
    }

    /**
     * Check if user has a minimum security level
     */
    public function hasLevel(int $level): bool
    {
        return $this->security_group >= $level;
    }

    /**
     * Check if user is admin (level 5+)
     */
    public function isAdmin(): bool
    {
        return $this->security_group >= 5;
    }

    /**
     * Check if user is super-admin (level 9)
     */
    public function isSuperAdmin(): bool
    {
        return $this->security_group >= 9;
    }

    /**
     * Check if user needs to force password update
     */
    public function needsPasswordUpdate(): bool
    {
        return $this->force_update;
    }
}
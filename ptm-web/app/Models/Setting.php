<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'description'];

    /**
     * The primary key is not an incrementing integer in the traditional
     * sense, but we use the default auto-increment id. 'key' is the
     * human-facing unique identifier.
     */
    public $incrementing = true;

    protected $keyType = 'int';

    /**
     * Look up a setting by its key, returning null if not found.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $row = static::where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    /**
     * Update (or create) a setting by key.
     */
    public static function set(string $key, ?string $value, ?string $description = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'description' => $description, 'updated_at' => now()]
        );
    }
}
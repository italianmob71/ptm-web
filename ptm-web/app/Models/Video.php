<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Video extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'filename',
        'path',
        'title',
        'description',
        'category',
        'source_url',
        'source_platform',
        'source_id',
        'file_size',
        'mime_type',
        'duration',
        'thumbnail_path',
        'published',
    ];

    protected $casts = [
        'published' => 'boolean',
        'file_size' => 'integer',
        'duration' => 'integer',
    ];

    protected $attributes = [
        'published' => false,
        'source_platform' => 'local',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('created_at');
    }

    public function scopeSearch($query, string $term)
    {
        $like = '%' . $term . '%';
        return $query->where(function ($q) use ($like) {
            $q->where('title', 'LIKE', $like)
              ->orWhere('slug', 'LIKE', $like)
              ->orWhere('description', 'LIKE', $like)
              ->orWhere('category', 'LIKE', $like);
        });
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * URL to the video file (local) or source URL (embedded).
     */
    public function getUrlAttribute(): ?string
    {
        if ($this->source_url) {
            return $this->source_url;
        }
        if ($this->path) {
            return asset($this->path);
        }
        return null;
    }

    /**
     * Embed URL for YouTube/Rumble.
     */
    public function getEmbedUrlAttribute(): ?string
    {
        if ($this->source_platform === 'youtube' && $this->source_id) {
            return 'https://www.youtube.com/embed/' . $this->source_id;
        }
        if ($this->source_platform === 'rumble' && $this->source_id) {
            return 'https://rumble.com/embed/' . $this->source_id . '/';
        }
        return null;
    }

    /**
     * Whether this is a local file or an embedded video.
     */
    public function getIsLocalAttribute(): bool
    {
        return !$this->source_url && $this->path;
    }

    public function getIsEmbeddedAttribute(): bool
    {
        return (bool) $this->source_url;
    }

    /**
     * Human-readable file size.
     */
    public function getFileSizeHumanAttribute(): ?string
    {
        if (!$this->file_size) return null;
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 1) . ' ' . $units[$i];
    }

    /**
     * Human-readable duration (mm:ss or h:mm:ss).
     */
    public function getDurationHumanAttribute(): ?string
    {
        if (!$this->duration) return null;
        $h = floor($this->duration / 3600);
        $m = floor(($this->duration % 3600) / 60);
        $s = $this->duration % 60;
        if ($h > 0) {
            return sprintf('%d:%02d:%02d', $h, $m, $s);
        }
        return sprintf('%d:%02d', $m, $s);
    }

    /**
     * Thumbnail URL — poster image, or auto-generated from source.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail_path) {
            return asset($this->thumbnail_path);
        }
        if ($this->source_platform === 'youtube' && $this->source_id) {
            return 'https://img.youtube.com/vi/' . $this->source_id . '/hqdefault.jpg';
        }
        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Extract video ID and platform from a URL.
     */
    public static function parseSourceUrl(string $url): array
    {
        // YouTube: youtube.com/watch?v=ID, youtu.be/ID, youtube.com/embed/ID
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return ['platform' => 'youtube', 'id' => $m[1]];
        }
        // Rumble: rumble.com/VIDEOID (extract the numeric or slug ID)
        if (preg_match('/rumble\.com\/(?:embed\/)?([a-zA-Z0-9_-]+)/', $url, $m)) {
            return ['platform' => 'rumble', 'id' => $m[1]];
        }
        return ['platform' => 'external', 'id' => null];
    }

    /**
     * Generate a unique slug.
     */
    public static function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $base = Str::slug($title);
        if (empty($base)) $base = 'video';
        $slug = $base;
        $count = 2;
        while (self::where('slug', $slug)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists()) {
            $slug = $base . '-' . $count++;
        }
        return $slug;
    }
}

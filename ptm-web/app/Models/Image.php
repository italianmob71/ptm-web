<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Image extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'filename',
        'path',
        'alt_text',
        'caption',
        'mime_type',
        'width',
        'height',
        'file_size',
        'category',
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'file_size' => 'integer',
    ];

    /**
     * Scope: search by slug, filename, alt text, or caption.
     */
    public function scopeSearch($query, string $term)
    {
        $term = '%' . $term . '%';
        return $query->where('slug', 'like', $term)
            ->orWhere('filename', 'like', $term)
            ->orWhere('alt_text', 'like', $term)
            ->orWhere('caption', 'like', $term)
            ->orWhere('category', 'like', $term);
    }

    /**
     * Scope: filter by category.
     */
    public function scopeCategory($query, ?string $category)
    {
        if ($category && $category !== 'all') {
            return $query->where('category', $category);
        }
        return $query;
    }

    /**
     * Scope: most recent first.
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('created_at');
    }

    /**
     * Full public URL to the image.
     */
    public function getUrlAttribute(): string
    {
        return asset($this->path);
    }

    /**
     * Human-readable file size.
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 0) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Generate a unique slug from a filename.
     */
    public static function generateUniqueSlug(string $filename): string
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        $slug = Str::slug($basename) ?: 'image-' . time();

        $count = 1;
        $originalSlug = $slug;
        while (self::withTrashed()->where('slug', $slug)->exists()) {
            $count++;
            $slug = $originalSlug . '-' . $count;
        }

        return $slug;
    }

    /**
     * All distinct categories for dropdown.
     */
    public static function categories(): array
    {
        return self::select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->toArray();
    }
}

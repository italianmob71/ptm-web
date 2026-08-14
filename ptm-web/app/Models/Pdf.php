<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Pdf extends Model
{
    use SoftDeletes;

    protected $table = 'pdfs';

    protected $fillable = [
        'slug',
        'filename',
        'path',
        'title',
        'description',
        'category',
        'file_size',
        'mime_type',
        'source_url',
    ];

    /**
     * Scope: search across slug, filename, title, description, category.
     */
    public function scopeSearch($query, string $term)
    {
        $like = '%' . $term . '%';
        return $query->where(function ($q) use ($like) {
            $q->where('slug', 'LIKE', $like)
              ->orWhere('filename', 'LIKE', $like)
              ->orWhere('title', 'LIKE', $like)
              ->orWhere('description', 'LIKE', $like)
              ->orWhere('category', 'LIKE', $like);
        });
    }

    /**
     * Scope: filter by category.
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: latest first.
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('created_at');
    }

    /**
     * Scope: published (PDFs don't have a published flag —
     * all records are considered available if they exist).
     */
    public function scopePublished($query)
    {
        return $query;
    }

    /**
     * Get distinct categories for dropdowns.
     */
    public static function categories()
    {
        return self::whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();
    }

    /**
     * Full URL to the PDF file.
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
        if (!$this->file_size) return '—';
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
     * Generate a unique slug from a filename.
     */
    public static function generateUniqueSlug(string $filename, ?int $excludeId = null): string
    {
        $base = Str::slug(pathinfo($filename, PATHINFO_FILENAME));
        if (empty($base)) {
            $base = 'pdf';
        }
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

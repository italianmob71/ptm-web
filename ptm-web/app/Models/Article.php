<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Article extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'title',
        'author_id',
        'summary',
        'content',
        'pdf_path',
        'published',
        'published_at',
    ];

    protected $casts = [
        'published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected $attributes = [
        'published' => false,
    ];

    /**
     * Relationship: author.
     */
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * Scope: published articles.
     */
    public function scopePublished($query)
    {
        return $query->where('published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Scope: newest first.
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('published_at')->orderByDesc('created_at');
    }

    /**
     * Scope: search across title, slug, summary, content.
     */
    public function scopeSearch($query, string $term)
    {
        $like = '%' . $term . '%';
        return $query->where(function ($q) use ($like) {
            $q->where('title', 'LIKE', $like)
              ->orWhere('slug', 'LIKE', $like)
              ->orWhere('summary', 'LIKE', $like)
              ->orWhere('content', 'LIKE', $like);
        });
    }

    /**
     * Scope: exclude NOSEARCH- prefixed slugs (for future global search).
     */
    public function scopeSearchable($query)
    {
        return $query->where('slug', 'NOT LIKE', 'NOSEARCH-%');
    }

    /**
     * Does this article have full text content?
     */
    public function getIsFullTextAttribute(): bool
    {
        return !empty($this->content);
    }

    /**
     * Does this article have a PDF?
     */
    public function getIsPdfAttribute(): bool
    {
        return !empty($this->pdf_path);
    }

    /**
     * Is this a PDF-only (NOSEARCH) article?
     */
    public function getIsPdfOnlyAttribute(): bool
    {
        return Str::startsWith($this->slug, 'NOSEARCH-');
    }

    /**
     * Get the PDF download URL.
     */
    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_path ? asset($this->pdf_path) : null;
    }

    /**
     * Get the public URL for this article.
     */
    public function getUrlAttribute(): string
    {
        return route('articles.show', $this->slug);
    }

    /**
     * Generate a unique slug from a title.
     */
    public static function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $base = Str::slug($title);
        if (empty($base)) {
            $base = 'article';
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

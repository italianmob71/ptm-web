<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TravelNote extends Model
{
    use SoftDeletes;

    protected $table = 'travel_notes';

    protected $fillable = [
        'author_id',
        'slug',
        'title',
        'content',
        'teaser_image_id',
        'biblical_reference',
        'location',
        'sort_order',
        'published',
        'published_at',
    ];

    protected $casts = [
        'published' => 'boolean',
        'published_at' => 'datetime',
        'sort_order' => 'integer',
    ];

    protected $attributes = [
        'published' => false,
        'sort_order' => 0,
    ];

    /**
     * Teaser image relationship.
     */
    public function teaserImage(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'teaser_image_id');
    }

    /**
     * Author relationship.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * Scope: published notes.
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
     * Scope: ordered by sort_order, then newest first.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderByDesc('created_at');
    }

    /**
     * Scope: latest first.
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('published_at')->orderByDesc('created_at');
    }

    /**
     * Scope: search.
     */
    public function scopeSearch($query, string $term)
    {
        $like = '%' . $term . '%';
        return $query->where(function ($q) use ($like) {
            $q->where('title', 'LIKE', $like)
              ->orWhere('slug', 'LIKE', $like)
              ->orWhere('content', 'LIKE', $like)
              ->orWhere('location', 'LIKE', $like)
              ->orWhere('biblical_reference', 'LIKE', $like);
        });
    }

    /**
     * Get teaser image URL (via relationship or null).
     */
    public function getTeaserUrlAttribute(): ?string
    {
        return $this->teaserImage?->url;
    }

    /**
     * Get the public URL.
     */
    public function getUrlAttribute(): string
    {
        return route('travel-notes.show', $this->slug);
    }

    /**
     * Generate a unique slug from a title.
     */
    public static function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $base = Str::slug($title);
        if (empty($base)) {
            $base = 'travel-note';
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

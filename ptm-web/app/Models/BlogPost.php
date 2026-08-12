<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'blog_posts';

    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
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

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    // Scope: only published posts
    public function scopePublished($query)
    {
        return $query->where('published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });
    }

    // Scope: ordered by published_at desc (latest first)
    public function scopeLatestFirst($query)
    {
        return $query->orderByRaw('COALESCE(published_at, created_at) DESC');
    }

    // Accessor: read time estimate from content length
    public function getReadTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, (int) ceil($wordCount / 200));
    }

    // Accessor: get excerpt or auto-generate from content
    public function getExcerptTextAttribute(): string
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }

        $plain = strip_tags($this->content);
        $plain = trim(preg_replace('/\s+/', ' ', $plain));

        if (strlen($plain) <= 200) {
            return $plain;
        }

        // Truncate at word boundary
        $truncated = substr($plain, 0, 200);
        $lastSpace = strrpos($truncated, ' ');
        if ($lastSpace !== false) {
            $truncated = substr($truncated, 0, $lastSpace);
        }

        return $truncated . '...';
    }
}

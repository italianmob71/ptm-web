<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'subtitle',
        'slug',
        'author_id',
        'body',
        'isbn_13',
        'isbn_10',
        'amazon_link',
        'lulu_link',
        'image_front',
        'image_back',
        'image_inner',
        'edition',
        'published_at',
        'published',
        'active',
        'page_count',
        'language',
        'price_usd',
    ];

    protected $casts = [
        'published_at' => 'date',
        'published' => 'boolean',
        'active' => 'boolean',
        'price_usd' => 'decimal:2',
    ];

    protected $attributes = [
        'published' => false,
        'active' => true,
        'language' => 'English',
    ];

    /**
     * Get the author that owns the book.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    // Scope for published books
    public function scopePublished($query)
    {
        return $query->where('published', true)->where('active', true);
    }

    // Scope for active books
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // Scope ordered by published date
    public function scopeLatest($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    // Accessor for full title
    public function getFullTitleAttribute(): string
    {
        return $this->subtitle ? "{$this->title}: {$this->subtitle}" : $this->title;
    }

    // Accessor for primary image
    public function getPrimaryImageAttribute(): string
    {
        return $this->image_front ?? 'default-book.jpg';
    }

    // Generate slug from title if not provided
    protected static function booted(): void
    {
        static::creating(function ($book) {
            if (empty($book->slug)) {
                $book->slug = \Illuminate\Support\Str::slug($book->title);
            }
        });
    }
}
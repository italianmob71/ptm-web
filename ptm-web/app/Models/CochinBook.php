<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CochinBook extends Model
{
    use SoftDeletes;

    protected $table = 'cochin_books';

    protected $fillable = [
        'slug',
        'title',
        'description',
        'discoveries',
        'manuscript',
        'status',
        'display_order',
        'total_chapters',
        'cover_image_id',
        'complete_pdf_id',
        'published',
        'published_at',
    ];

    protected $casts = [
        'published'     => 'boolean',
        'published_at'  => 'datetime',
        'total_chapters'=> 'integer',
    ];

    protected $attributes = [
        'status'         => 'wip',
        'total_chapters' => 0,
        'published'      => false,
    ];

    /**************************************************************************
     * Relationships
     *************************************************************************/

    public function chapters(): HasMany
    {
        return $this->hasMany(CochinChapter::class, 'book_id')
                    ->orderBy('chapter_number');
    }

    public function publishedChapters(): HasMany
    {
        return $this->chapters()->where('published', true);
    }

    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'cover_image_id');
    }

    public function completePdf(): BelongsTo
    {
        return $this->belongsTo(Pdf::class, 'complete_pdf_id');
    }

    /**************************************************************************
     * Scopes
     *************************************************************************/

    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('title');
    }

    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('created_at');
    }

    /**
     * Canonical New Testament book order (1-27).
     * Used to auto-assign display_order when creating books.
     */
    public static array $canonicalOrder = [
        'matthew'      => 1,
        'mark'         => 2,
        'luke'         => 3,
        'john'         => 4,
        'acts'         => 5,
        'romans'       => 6,
        '1-corinthians' => 7,
        '2-corinthians' => 8,
        'galatians'    => 9,
        'ephesians'    => 10,
        'philippians'  => 11,
        'colossians'   => 12,
        '1-thessalonians' => 13,
        '2-thessalonians' => 14,
        '1-timothy'    => 15,
        '2-timothy'    => 16,
        'titus'        => 17,
        'philemon'     => 18,
        'hebrews'      => 19,
        'james'        => 20,
        '1-peter'      => 21,
        '2-peter'      => 22,
        '1-john'       => 23,
        '2-john'       => 24,
        '3-john'       => 25,
        'jude'         => 26,
        'revelation'   => 27,
    ];

    /**
     * Look up canonical display_order from a slug.
     * Returns 999 (end of list) for unknown/unmatched slugs.
     */
    public static function canonicalOrderForSlug(string $slug): int
    {
        // Try exact match
        if (isset(self::$canonicalOrder[$slug])) {
            return self::$canonicalOrder[$slug];
        }
        // Normalize: strip common prefixes/suffixes
        $normalized = strtolower($slug);
        $normalized = preg_replace('/^cambridge-/', '', $normalized);
        $normalized = preg_replace('/^cochin-hebrew-/', '', $normalized);
        $normalized = preg_replace('/^cochin-/', '', $normalized);
        $normalized = preg_replace('/-cambridge.*$/', '', $normalized);
        $normalized = preg_replace('/-ms-.*$/', '', $normalized);
        $normalized = preg_replace('/-scroll.*$/', '', $normalized);
        // Try the normalized slug
        if (isset(self::$canonicalOrder[$normalized])) {
            return self::$canonicalOrder[$normalized];
        }
        // Try matching individual words (e.g. "revelation" within a complex slug)
        foreach (self::$canonicalOrder as $bookSlug => $order) {
            if (strpos($normalized, $bookSlug) !== false) {
                return $order;
            }
        }
        return 999;
    }

    /**************************************************************************
     * Helpers
     *************************************************************************/

    public function getIsWipAttribute(): bool
    {
        return $this->status === 'wip';
    }

    public function getIsCompleteAttribute(): bool
    {
        return $this->status === 'complete';
    }

    public function getProgressPercentAttribute(): int
    {
        if ($this->total_chapters === 0) return 0;
        $published = $this->publishedChapters()->count();
        return (int) round(($published / $this->total_chapters) * 100);
    }

    public function getChapterCountAttribute(): int
    {
        return $this->publishedChapters()->count();
    }

    public static function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;
        while (self::where('slug', $slug)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }
        return $slug;
    }
}

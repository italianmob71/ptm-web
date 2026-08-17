<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CochinChapter extends Model
{
    protected $table = 'cochin_chapters';

    protected $fillable = [
        'book_id',
        'chapter_number',
        'title',
        'pdf_id',
        'published',
        'published_at',
    ];

    protected $casts = [
        'chapter_number' => 'integer',
        'published'      => 'boolean',
        'published_at'   => 'datetime',
    ];

    protected $attributes = [
        'published' => false,
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(CochinBook::class, 'book_id');
    }

    public function pdf(): BelongsTo
    {
        return $this->belongsTo(Pdf::class, 'pdf_id');
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, 'video_id');
    }

    /**************************************************************************
     * Helpers
     *************************************************************************/

    public function getDisplayTitleAttribute(): string
    {
        return $this->title ?? "Chapter {$this->chapter_number}";
    }

    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('chapter_number');
    }
}

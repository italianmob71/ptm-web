<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialStudy extends Model
{
    protected $fillable = ['pdf_id', 'display_order'];

    public function pdf(): BelongsTo
    {
        return $this->belongsTo(Pdf::class);
    }
}

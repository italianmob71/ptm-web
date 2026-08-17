<?php

namespace App\Http\Controllers;

use App\Models\CochinBook;
use App\Services\RelatedContentFinder;

class CochinBookController extends Controller
{
    /**
     * Show a single Cochin book with its chapters, downloads, and videos.
     */
    public function show(string $slug)
    {
        $book = CochinBook::published()
            ->with(['chapters' => function ($q) {
                $q->published()->ordered();
            }, 'chapters.pdf', 'coverImage', 'completePdf'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Related content from across the ecosystem
        $related = (new RelatedContentFinder())->findBySlug(
            $book->slug,
            exclude: [CochinBook::class => [$book->id]],
            limit: 6
        );

        return view('cochin.show', [
            'book'    => $book,
            'related' => $related,
            'title'   => $book->title,
        ]);
    }
}

<?php

namespace App\Services;

use App\Models\Article;
use App\Models\BlogPost;
use App\Models\Book;
use App\Models\CochinBook;
use App\Models\Image;
use App\Models\Pdf;
use App\Models\Video;
use App\Models\TravelNote;
use Illuminate\Support\Collection;

/**
 * RelatedContentFinder — slug-token relevance engine.
 *
 * Tokenizes a source entity's slug, searches across all content types
 * by matching tokens in their slugs + titles, scores each candidate by
 * the number of shared tokens, and returns a sorted Collection of unified
 * result objects.
 *
 * Usage:
 *   $finder = new RelatedContentFinder();
 *   $related = $finder->find($sourceModel, limit: 5);
 *
 * Grouped (for tabbed display):
 *   $grouped = $finder->findBySlugGrouped($slug, $exclude, minRelevancy: 50);
 *   // Returns ['articles' => Collection, 'cochin-books' => Collection, ...]
 */
class RelatedContentFinder
{
    protected array $stopWords = [
        'the', 'a', 'an', 'and', 'or', 'of', 'in', 'on', 'at', 'to',
        'for', 'is', 'are', 'was', 'were', 'be', 'been', 'being',
        'by', 'with', 'from', 'as', 'into', 'about', 'how', 'what',
        'why', 'when', 'where', 'which', 'who', 'that', 'this', 'it',
        'its', 'has', 'have', 'had', 'do', 'does', 'did', 'not', 'no',
        'all', 'new', 'more', 'most', 'some', 'any', 'if', 'than', 'then',
        'ch', 'chapter', 'part', 'pt', 'vol', 'section', 'sec',
    ];

    /**
     * Content types to search, with their model class + URL route name.
     */
    protected array $contentTypes = [
        'article' => [
            'model' => Article::class,
            'route' => 'articles.show',
            'title_col' => 'title',
            'tab' => 'articles',
        ],
        'blog' => [
            'model' => BlogPost::class,
            'route' => 'blog.show',
            'title_col' => 'title',
            'tab' => 'articles',
        ],
        'travel-note' => [
            'model' => TravelNote::class,
            'route' => 'travel-notes.show',
            'title_col' => 'title',
            'tab' => 'articles',
        ],
        'cochin-book' => [
            'model' => CochinBook::class,
            'route' => 'cochin.show',
            'title_col' => 'title',
            'tab' => 'cochin-books',
        ],
        'book' => [
            'model' => Book::class,
            'route' => 'books.show',
            'title_col' => 'title',
            'tab' => 'books',
        ],
        'pdf' => [
            'model' => Pdf::class,
            'route' => 'pdfs.show',
            'title_col' => 'title',
            'tab' => 'pdfs',
        ],
        'video' => [
            'model' => Video::class,
            'route' => 'videos.show',
            'title_col' => 'title',
            'tab' => 'videos',
        ],
        'image' => [
            'model' => Image::class,
            'route' => 'images.show',
            'title_col' => 'alt_text',
            'tab' => 'images',
        ],
    ];

    /**
     * Tab definitions: key => label, with display order.
     */
    public array $tabs = [
        'articles'     => ['label' => 'Articles',     'order' => 1],
        'cochin-books' => ['label' => 'Cochin Books',  'order' => 2],
        'books'        => ['label' => 'Books',         'order' => 3],
        'pdfs'         => ['label' => 'PDFs',          'order' => 4],
        'videos'       => ['label' => 'Videos',        'order' => 5],
        'images'       => ['label' => 'Images',        'order' => 6],
    ];

    /**************************************************************************
     * Single flat list (backward-compatible)
     *************************************************************************/

    public function find(mixed $source, int $limit = 6, array $exclude = []): Collection
    {
        $slug = $source->slug ?? null;
        if (!$slug) {
            return collect();
        }
        $sourceClass = get_class($source);
        $sourceId = $source->id;
        if (!isset($exclude[$sourceClass])) {
            $exclude[$sourceClass] = [];
        }
        $exclude[$sourceClass][] = $sourceId;
        return $this->findBySlug($slug, $exclude, $limit);
    }

    public function findBySlug(string $slug, array $exclude = [], int $limit = 6): Collection
    {
        $all = $this->searchAllTypes($slug, $exclude);
        return $all->sortBy([
            ['relevancy', 'desc'],
            ['score', 'desc'],
            ['title', 'asc'],
        ])->take($limit)->values();
    }

    /**************************************************************************
     * Grouped by tab (for tabbed See Also component)
     *
     * Returns associative array: [tabKey => Collection, ...]
     * Only includes tabs with at least 1 result above minRelevancy.
     * Each item has: id, title, slug, url, type, type_label, score, relevancy
     *************************************************************************/

    public function findBySlugGrouped(string $slug, array $exclude = [], int $minRelevancy = 50): array
    {
        $all = $this->searchAllTypes($slug, $exclude, $minRelevancy);

        // Group by tab key
        $grouped = [];
        foreach ($all as $item) {
            $tabKey = $this->contentTypes[$item->type]['tab'] ?? 'other';
            if (!isset($grouped[$tabKey])) {
                $grouped[$tabKey] = collect();
            }
            $grouped[$tabKey]->push($item);
        }

        // Sort each group by relevancy desc, then score desc, then title asc
        foreach ($grouped as $key => $items) {
            $grouped[$key] = $items->sortBy([
                ['relevancy', 'desc'],
                ['score', 'desc'],
                ['title', 'asc'],
            ])->values();
        }

        // Sort tabs by their defined order
        $ordered = [];
        uksort($grouped, function ($a, $b) {
            $oa = $this->tabs[$a]['order'] ?? 99;
            $ob = $this->tabs[$b]['order'] ?? 99;
            return $oa <=> $ob;
        });

        return $grouped;
    }

    /**************************************************************************
     * Core search — scans all content types, returns flat Collection
     *************************************************************************/

    protected function searchAllTypes(string $slug, array $exclude = [], int $minRelevancy = 0): Collection
    {
        $tokens = $this->tokenize($slug);
        if (empty($tokens)) {
            return collect();
        }

        $tokenCount = count($tokens);
        $results = collect();

        foreach ($this->contentTypes as $typeName => $config) {
            $modelClass = $config['model'];
            $routeName = $config['route'];
            $titleCol = $config['title_col'];

            $query = $modelClass::published();
            $query->where('slug', 'NOT LIKE', 'NOSEARCH-%');

            $query->where(function ($q) use ($tokens, $titleCol) {
                foreach ($tokens as $token) {
                    $q->orWhere('slug', 'LIKE', "%{$token}%");
                }
                foreach ($tokens as $token) {
                    $q->orWhere($titleCol, 'LIKE', "%{$token}%");
                }
            });

            $select = ['id', 'slug', $titleCol];
            if (in_array($typeName, ['image', 'pdf', 'video'])) {
                $select[] = 'filename';
            }
            if ($typeName === 'book') $select[] = 'image_front';
            if ($typeName === 'video') { $select[] = 'source_platform'; $select[] = 'source_id'; }
            $rows = $query->get($select);

            foreach ($rows as $row) {
                $excludedIds = $exclude[$modelClass] ?? [];
                if (in_array($row->id, $excludedIds)) {
                    continue;
                }

                $slugTokens = $this->tokenize($row->slug);
                $titleTokens = $this->tokenize($row->{$titleCol});

                $slugScoreRaw = count(array_intersect($tokens, $slugTokens));
                $titleScoreRaw = count(array_intersect($tokens, $titleTokens));

                if ($slugScoreRaw + $titleScoreRaw === 0) {
                    continue;
                }

                // Relevancy = matched tokens / source tokens (as percentage)
                $relevancy = (int) round(($slugScoreRaw + $titleScoreRaw) / $tokenCount * 100);

                // Apply minimum relevancy filter
                if ($relevancy < $minRelevancy) {
                    continue;
                }

                $totalScore = $slugScoreRaw * 2 + $titleScoreRaw;

                // Teaser image
                $teaserUrl = null;
                if ($typeName === 'image') {
                    $teaserUrl = $row->url;
                } elseif ($typeName === 'book' && $row->image_front) {
                    $teaserUrl = asset('images/books/' . $row->image_front);
                } elseif ($typeName === 'video' && $row->source_platform === 'youtube' && $row->source_id) {
                    $teaserUrl = 'https://img.youtube.com/vi/' . $row->source_id . '/hqdefault.jpg';
                } elseif (method_exists($row, 'teaserImage') && $row->teaserImage) {
                    $teaserUrl = $row->teaserImage->url;
                } elseif ($row->relationLoaded('author') && $row->author && $row->author->image) {
                    $teaserUrl = asset('images/authors/' . $row->author->image);
                }

                $results->push((object) [
                    'id'          => $row->id,
                    'title'       => $row->{$titleCol} ?? ($row->filename ?? null) ?? $row->slug,
                    'slug'        => $row->slug,
                    'url'         => route($routeName, $row->slug),
                    'type'        => $typeName,
                    'type_label'  => $this->typeLabel($typeName),
                    'score'       => $totalScore,
                    'relevancy'   => min(100, max(1, $relevancy)),
                    'teaser_url'  => $teaserUrl,
                    'tab'         => $config['tab'],
                ]);
            }
        }

        return $results;
    }

    /**************************************************************************
     * Helpers
     *************************************************************************/

    public function tokenize(?string $slug): array
    {
        if ($slug === null) {
            return [];
        }
        $slug = preg_replace('/^NOSEARCH-/i', '', $slug);
        $parts = preg_split('/[-_]/', strtolower(trim($slug)));

        $tokens = [];
        foreach ($parts as $p) {
            $p = trim($p);
            if ($p === '' || in_array($p, $this->stopWords) || isset($tokens[$p])) {
                continue;
            }
            $tokens[$p] = $p;
        }

        $stemmed = [];
        foreach ($tokens as $t) {
            $len = strlen($t);
            $lastChar = substr($t, -1);
            $lastTwo = substr($t, -2);
            $lastThree = substr($t, -3);

            if ($lastChar === 's' && $len > 4 && $lastTwo !== 'ss') {
                if ($lastThree === 'ses' || $lastThree === 'sis' || $lastTwo === 'us') {
                    // Proper noun — keep as-is
                } else {
                    $t = substr($t, 0, -1);
                }
            }
            if (!in_array($t, $stemmed) && !in_array($t, $this->stopWords)) {
                $stemmed[] = $t;
            }
        }

        return $stemmed;
    }

    protected function typeLabel(string $type): string
    {
        return match ($type) {
            'article'      => 'Article',
            'blog'         => 'Blog Post',
            'travel-note'  => 'Travel Note',
            'cochin-book'  => 'Cochin Book',
            'book'         => 'Book',
            'pdf'          => 'PDF',
            'video'        => 'Video',
            'image'        => 'Image',
            default        => ucfirst(str_replace('-', ' ', $type)),
        };
    }
}

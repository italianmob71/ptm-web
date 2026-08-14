<?php

namespace App\Services;

use App\Models\Article;
use App\Models\BlogPost;
use App\Models\Book;
use App\Models\Image;
use App\Models\Pdf;
use App\Models\Video;
use App\Models\TravelNote;
use Illuminate\Support\Collection;

/**
 * RelatedContentFinder — slug-token relevance engine.
 *
 * Tokenizes a source entity's slug, searches across all content types
 * (articles, blog posts, travel notes) by matching tokens in their slugs,
 * scores each candidate by the number of shared tokens, and returns a
 * sorted Collection of unified result objects.
 *
 * Usage:
 *   $finder = new RelatedContentFinder();
 *   $related = $finder->find($sourceModel, limit: 5);
 *   // Returns Collection of objects: {id, title, slug, url, type, score, teaser_url}
 *
 * Or search by an arbitrary slug string:
 *   $related = $finder->findBySlug('cochin-hebrew-matthew-chapter-1', exclude: [Article::class => [1]]);
 */
class RelatedContentFinder
{
    /**
     * Common English stop words to filter from tokens.
     * Kept small — only words that add noise to slug matching.
     */
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
     * Add new types here as the ecosystem grows.
     */
    protected array $contentTypes = [
        'article' => [
            'model' => Article::class,
            'route' => 'articles.show',
            'title_col' => 'title',
        ],
        'blog' => [
            'model' => BlogPost::class,
            'route' => 'blog.show',
            'title_col' => 'title',
        ],
        'travel-note' => [
            'model' => TravelNote::class,
            'route' => 'travel-notes.show',
            'title_col' => 'title',
        ],
        'book' => [
            'model' => Book::class,
            'route' => 'books.show',
            'title_col' => 'title',
        ],
        'pdf' => [
            'model' => Pdf::class,
            'route' => 'pdfs.show',
            'title_col' => 'title',
        ],
        'video' => [
            'model' => Video::class,
            'route' => 'videos.show',
            'title_col' => 'title',
        ],
        'image' => [
            'model' => Image::class,
            'route' => 'images.show',
            'title_col' => 'alt_text',  // images use alt_text, not title
        ],
    ];

    /**
     * Find related content from a source model instance.
     *
     * @param mixed  $source  An Eloquent model with a slug attribute
     * @param int    $limit   Max results to return
     * @param array  $exclude Types to exclude: ['Article' => [ids...], 'BlogPost' => [ids...]]
     * @return Collection  Items: {id, title, slug, url, type, score, teaser_url}
     */
    public function find(mixed $source, int $limit = 6, array $exclude = []): Collection
    {
        $slug = $source->slug ?? null;
        if (!$slug) {
            return collect();
        }

        // Auto-exclude the source itself
        $sourceClass = get_class($source);
        $sourceId = $source->id;
        if (!isset($exclude[$sourceClass])) {
            $exclude[$sourceClass] = [];
        }
        $exclude[$sourceClass][] = $sourceId;

        return $this->findBySlug($slug, $exclude, $limit);
    }

    /**
     * Find related content by an arbitrary slug string.
     *
     * @param string $slug    The slug to tokenize and search with
     * @param array  $exclude Types to exclude: [ModelClass::class => [ids...]]
     * @param int    $limit   Max results
     * @return Collection
     */
    public function findBySlug(string $slug, array $exclude = [], int $limit = 6): Collection
    {
        $tokens = $this->tokenize($slug);

        if (empty($tokens)) {
            return collect();
        }

        $results = collect();

        foreach ($this->contentTypes as $typeName => $config) {
            $modelClass = $config['model'];
            $routeName = $config['route'];
            $titleCol = $config['title_col'];

            // Build query: published items whose slug contains ANY token.
            // Exclude NOSEARCH- prefixed slugs — they're hidden from search
            // AND from See Also relevancy results. The NOT LIKE must wrap
            // both slug and title searches inside a single closure so the
            // OR conditions don't bypass the NOSEARCH filter.
            $query = $modelClass::published();
            $query->where('slug', 'NOT LIKE', 'NOSEARCH-%');

            $query->where(function ($q) use ($tokens, $titleCol) {
                // Search slug (preferred — weighted 2x in scoring)
                foreach ($tokens as $token) {
                    $q->orWhere('slug', 'LIKE', "%{$token}%");
                }
                // Also search title for bonus scoring (broadens recall)
                foreach ($tokens as $token) {
                    $q->orWhere($titleCol, 'LIKE', "%{$token}%");
                }
            });

            // Select columns needed for scoring + teaser
            $select = ['id', 'slug', $titleCol];
            if (in_array($typeName, ['image', 'pdf', 'video'])) {
                $select[] = 'filename';
            }
            if ($typeName === 'book') $select[] = 'image_front';
            if ($typeName === 'video') { $select[] = 'source_platform'; $select[] = 'source_id'; }
            $rows = $query->get($select);

            // Score each row
            foreach ($rows as $row) {
                // Skip excluded IDs
                $excludedIds = $exclude[$modelClass] ?? [];
                if (in_array($row->id, $excludedIds)) {
                    continue;
                }

                $slugTokens = $this->tokenize($row->slug);
                $titleTokens = $this->tokenize($row->{$titleCol});

                // Score: count of source tokens found in the candidate's slug
                // Slug matches are weighted 2x, title matches 1x
                $slugScore = count(array_intersect($tokens, $slugTokens)) * 2;
                $titleScore = count(array_intersect($tokens, $titleTokens));
                $totalScore = $slugScore + $titleScore;

                if ($totalScore === 0) {
                    continue;
                }

                // Build teaser image URL based on content type
                $teaserUrl = null;
                if ($typeName === 'image') {
                    // Images are their own teaser
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

                // Relevancy = matched tokens / source tokens (as integer percentage)
                // If source has 3 tokens and candidate matches 1 slug token + 0 title
                // tokens, that's 1/3 = 33%. Slug matches count as the match unit
                // (not the 2x weighted score — that was for ranking, not percentage).
                $slugScoreRaw = count(array_intersect($tokens, $slugTokens));
                $titleScoreRaw = count(array_intersect($tokens, $titleTokens));
                $totalScore = $slugScoreRaw * 2 + $titleScoreRaw;
                $relevancy = (int) round(($slugScoreRaw + $titleScoreRaw) / count($tokens) * 100);

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
                ]);
            }
        }

        // Sort by relevancy descending (primary), then score descending (tiebreak),
        // then title for deterministic ordering
        return $results->sortBy([
            ['relevancy', 'desc'],
            ['score', 'desc'],
            ['title', 'asc'],
        ])->take($limit)->values();
    }

    /**
     * Tokenize a slug: split on hyphens, filter stop words, lowercase.
     *
     * "cochin-hebrew-matthew-ch1" → ["cochin", "hebrew", "matthew"]
     * "well-of-moses" → ["well", "moses"]
     *
     * @return string[]
     */
    public function tokenize(?string $slug): array
    {
        if ($slug === null) {
            return [];
        }
        // Strip NOSEARCH- prefix if present
        $slug = preg_replace('/^NOSEARCH-/i', '', $slug);

        // Split on hyphens (and underscores, just in case)
        $parts = preg_split('/[-_]/', strtolower(trim($slug)));

        // Filter: remove empty, remove stop words, dedupe
        $tokens = [];
        foreach ($parts as $p) {
            $p = trim($p);
            if ($p === '' || in_array($p, $this->stopWords) || isset($tokens[$p])) {
                continue;
            }
            $tokens[$p] = $p;
        }

        // Light stemming: strip trailing 's' for basic plural normalization.
        // "altars" → "altar", "kings" → "king"
        // But preserve proper nouns: "moses", "jesus", genesis" — these end in 's'
        // but are NOT plurals. Rule: don't stem words ending in 'ses', 'us', 'is',
        // or words shorter than 5 chars (<=4) to avoid butchering names.
        $stemmed = [];
        foreach ($tokens as $t) {
            $len = strlen($t);
            $lastChar = substr($t, -1);
            $lastTwo = substr($t, -2);
            $lastThree = substr($t, -3);

            if ($lastChar === 's' && $len > 4 && $lastTwo !== 'ss') {
                // Don't stem words like "moses", "genesis", "isis" — ends in 'ses'/'sis'/'is'
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

    /**
     * Human-readable label for a content type.
     */
    protected function typeLabel(string $type): string
    {
        return match ($type) {
            'article'     => 'Article',
            'blog'        => 'Blog Post',
            'travel-note' => 'Travel Note',
            'book'        => 'Book',
            'pdf'         => 'PDF',
            'video'       => 'Video',
            'image'       => 'Image',
            default       => ucfirst(str_replace('-', ' ', $type)),
        };
    }
}

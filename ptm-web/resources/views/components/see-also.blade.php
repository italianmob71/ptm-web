@props([
    'source'  => null,   // An Eloquent model with a slug (BlogPost, Article, TravelNote)
    'slug'    => null,   // Or pass a raw slug string instead of a model
    'limit'   => 6,      // Max items to show
])

@php
    use App\Services\RelatedContentFinder;

    // Determine source slug — either from model or explicit string
    $sourceSlug = null;
    $excludeModel = null;

    if ($source) {
        $sourceSlug = $source->slug ?? null;
        $excludeModel = $source;
    } elseif ($slug) {
        $sourceSlug = $slug;
    }

    if (!$sourceSlug) {
        return; // No slug → render nothing
    }

    $finder = new RelatedContentFinder();

    if ($excludeModel) {
        $related = $finder->find($excludeModel, limit: $limit);
    } else {
        $related = $finder->findBySlug($sourceSlug, limit: $limit);
    }

    if ($related->isEmpty()) {
        return; // No results → render nothing
    }

    // Map content types to icon + color
    $typeConfig = [
        'article'     => ['icon' => 'icon-article',    'color' => 'var(--color-accent)'],
        'blog'        => ['icon' => 'icon-blog',        'color' => 'var(--color-accent)'],
        'travel-note' => ['icon' => 'icon-travel-note','color' => 'var(--color-accent)'],
        'book'        => ['icon' => 'icon-book',       'color' => 'var(--color-accent)'],
        'pdf'         => ['icon' => 'icon-pdf',        'color' => '#E9352F'],
        'image'       => ['icon' => 'icon-image',      'color' => 'var(--color-text-muted)'],
        'video'       => ['icon' => 'icon-video',      'color' => 'var(--color-text-muted)'],
    ];

    // Calculate max score for percentage display
    $maxScore = $related->first()->score ?? 1;
@endphp
<div class="see-also" style="
    max-width: 50rem;
    margin: 2.5rem auto;
">
    <div style="
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg, 12px);
        background: var(--color-surface);
        overflow: hidden;
    ">
        {{-- Header bar --}}
        <div style="
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid var(--color-border);
            background: var(--color-surface-2, rgba(128,128,128,0.06));
            display: flex;
            align-items: center;
            gap: 0.5rem;
        ">
            <svg style="width:1.25rem; height:1.25rem; color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            <h2 style="
                font-family: var(--font-serif, Georgia, serif);
                font-size: 1.125rem;
                font-weight: 700;
                color: var(--color-text);
                margin: 0;
            ">See Also</h2>
            <span style="
                margin-left: auto;
                font-size: 0.75rem;
                color: var(--color-text-faint);
            ">{{ $related->count() }} related</span>
        </div>

        {{-- Column headers --}}
        <div style="
            display: grid;
            grid-template-columns: 5rem 4rem 1.75rem 1fr;
            gap: 0.75rem;
            padding: 0.5rem 1.25rem;
            border-bottom: 1px solid var(--color-border);
            background: var(--color-surface-2, rgba(128,128,128,0.03));
        ">
            <span style="
                font-size: 0.6875rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: var(--color-text-faint);
                text-align: right;
            ">Relevancy</span>
            <span style="
                font-size: 0.6875rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: var(--color-text-faint);
            ">Match</span>
            <span style="
                font-size: 0.6875rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: var(--color-text-faint);
            ">Type</span>
            <span style="
                font-size: 0.6875rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: var(--color-text-faint);
            ">Title</span>
        </div>

        {{-- Item list --}}
        <div>
            @foreach ($related as $item)
                @php
                    $cfg = $typeConfig[$item->type] ?? $typeConfig['article'];
                    $pct = $item->relevancy;
                @endphp
                <a href="{{ $item->url }}"
                   style="
                       display: grid;
                       grid-template-columns: 5rem 4rem 1.75rem 1fr;
                       gap: 0.75rem;
                       align-items: center;
                       padding: 0.75rem 1.25rem;
                       text-decoration: none;
                       color: var(--color-text);
                       border-bottom: 1px solid var(--color-border);
                       transition: background 0.15s ease;
                   "
                   onmouseover="this.style.background='var(--color-surface-2, rgba(128,128,128,0.06))';"
                   onmouseout="this.style.background='transparent';"
                >
                    {{-- Relevancy percentage --}}
                    <span style="
                        text-align: right;
                        font-size: 0.8125rem;
                        font-weight: 600;
                        color: var(--color-text-faint);
                        font-variant-numeric: tabular-nums;
                    ">{{ $pct }}%</span>

                    {{-- Bar indicator --}}
                    <span style="
                        height: 4px;
                        border-radius: 2px;
                        background: var(--color-surface-2, rgba(128,128,128,0.1));
                        overflow: hidden;
                        position: relative;
                    ">
                        <span style="
                            display: block;
                            height: 100%;
                            width: {{ $pct }}%;
                            background: {{ $cfg['color'] }};
                            border-radius: 2px;
                            transition: width 0.3s ease;
                        "></span>
                    </span>

                    {{-- Type icon --}}
                    <svg style="
                        width: 1.25rem;
                        height: 1.25rem;
                        color: {{ $cfg['color'] }};
                        display: block;
                    " aria-hidden="true">
                        <use href="#{{ $cfg['icon'] }}"></use>
                    </svg>

                    {{-- Title + type label --}}
                    <span style="min-width: 0;">
                        <span style="
                            display: block;
                            font-size: 0.875rem;
                            font-weight: 500;
                            color: var(--color-text);
                            white-space: nowrap;
                            overflow: hidden;
                            text-overflow: ellipsis;
                        ">{{ $item->title }}</span>
                        <span style="
                            display: block;
                            font-size: 0.6875rem;
                            color: var(--color-text-faint);
                            text-transform: uppercase;
                            letter-spacing: 0.05em;
                            margin-top: 1px;
                        ">{{ $item->type_label }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</div>

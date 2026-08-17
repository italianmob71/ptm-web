@extends('layouts.app')

@section('content')
<!-- Articles Section -->
<section class="articles-section" aria-labelledby="articles-heading">
    <div class="articles-card">
        <!-- Section Title Bar -->
        <header class="articles-card__header">
            <div class="articles-card__title-lines">
                <span class="articles-card__line1">Project Truth Ministries &ndash; Presents</span>
                <span class="articles-card__line2">Scholarly Articles</span>
            </div>
            <form method="GET" action="{{ route('articles.index') }}" class="articles-search-form">
                <input type="search"
                       name="q"
                       value="{{ $search ?? '' }}"
                       placeholder="Search articles..."
                       class="articles-search-input"
                       aria-label="Search articles">
                <button type="submit" class="articles-search-btn">
                    <svg class="btn-icon" aria-hidden="true"><use xlink:href="#icon-search"></use></svg>
                    Search
                </button>
            </form>
        </header>

        <!-- Search Results Bar -->
        @if (request('q'))
            <div class="articles-search-results-bar">
                <p>Search results for &ldquo;<strong>{{ $search }}</strong>&rdquo; — {{ $articles->total() }} article{{ $articles->total() === 1 ? '' : 's' }} found.</p>
                <a href="{{ route('articles.index') }}" class="articles-search-clear">Clear search &times;</a>
            </div>
        @endif

        <!-- Articles Grid -->
        @if ($articles->isNotEmpty())
            <div class="articles-grid">
                @foreach ($articles as $article)
                    <article class="article-card">
                        <div class="article-card__content">
                            <time class="article-card__date" datetime="{{ $article->published_at?->toDateString() }}">
                                {{ $article->published_at?->format('F j, Y') ?? $article->created_at->format('F j, Y') }}
                            </time>
                            <h3 class="article-card__title">
                                <a href="{{ route('articles.show', $article->slug) }}">
                                    {{ $article->title }}
                                </a>
                            </h3>
                            @if ($article->sub_title)
                                <p class="article-card__subtitle">{{ $article->sub_title }}</p>
                            @endif
                            @if ($article->summary)
                                <p class="article-card__summary">{{ $article->summary }}</p>
                            @elseif ($article->content)
                                <p class="article-card__summary">{{ Str::limit(strip_tags($article->content), 180) }}</p>
                            @endif
                            <p class="article-card__author">
                                By {{ $article->author?->full_name ?? 'Unknown' }}
                            </p>
                            <a href="{{ route('articles.show', $article->slug) }}" class="article-card__btn">Read Article <span aria-hidden="true">&rarr;</span></a>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($articles->hasPages())
                <div class="articles-pagination">
                    {{ $articles->links() }}
                </div>
            @endif
        @else
            <div class="articles-empty">
                @if (request('q'))
                    <p>No articles found for &ldquo;{{ $search }}&rdquo;.</p>
                    <a href="{{ route('articles.index') }}" class="articles-search-clear">Clear search &times;</a>
                @else
                    <p>No articles have been published yet.</p>
                @endif
            </div>
        @endif
    </div>
</section>

<style>
    /* ===== Articles Index — mirrors Blog index ===== */
    .articles-section { padding: 0 1.5rem 4rem; }
    @media (min-width: 768px) { .articles-section { padding: 0 2rem 5rem; } }

    .articles-card {
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        max-width: 80rem;
        margin: 4rem auto 0;
    }

    .articles-card__header {
        background: var(--color-bg);
        border-bottom: 1px solid var(--color-border);
        padding: 2rem 2.5rem;
    }
    @media (min-width: 768px) { .articles-card__header { padding: 2.5rem 3rem; } }

    .articles-card__title-lines { display: flex; flex-direction: column; gap: 0.25rem; }
    .articles-card__line1 {
        font-family: var(--font-sans);
        font-size: 0.875rem; font-weight: 500;
        letter-spacing: 0.1em; text-transform: uppercase;
        color: var(--color-accent);
    }
    .articles-card__line2 {
        font-family: var(--font-serif);
        font-size: 2rem; font-weight: 600; line-height: 1.2;
        color: var(--color-text);
    }
    @media (min-width: 768px) { .articles-card__line2 { font-size: 2.5rem; } }

    .articles-grid {
        display: grid; grid-template-columns: 1fr; gap: 1.5rem; padding: 2rem;
    }
    @media (min-width: 768px) {
        .articles-grid { grid-template-columns: repeat(2, 1fr); padding: 2.5rem 3rem 3rem; }
    }
    @media (min-width: 1024px) { .articles-grid { grid-template-columns: repeat(3, 1fr); } }

    .article-card {
        background: var(--color-bg);
        border: 1px solid var(--color-border-soft);
        border-radius: var(--radius-md);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        display: flex; flex-direction: column;
    }
    .article-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--color-accent);
    }

    .article-card__content { padding: 1.5rem; display: flex; flex-direction: column; flex: 1; }

    .article-card__date {
        display: block; font-size: 0.75rem;
        color: var(--color-accent); margin-bottom: 0.5rem;
    }

    .article-card__title {
        font-family: var(--font-serif);
        font-size: 1.125rem; font-weight: 600; line-height: 1.4;
        margin: 0 0 0.75rem; color: var(--color-text);
    }
    .article-card__title a { color: inherit; text-decoration: none; }

    .article-card__subtitle {
        font-family: var(--font-serif);
        font-size: 0.875rem; font-weight: 400; font-style: italic;
        color: var(--color-text-muted); margin: -0.5rem 0 0.75rem;
    }

    .article-card__summary {
        font-size: 0.875rem; line-height: 1.65;
        color: var(--color-text-muted); margin: 0 0 1rem; flex: 1;
    }

    .article-card__author { font-size: 0.8125rem; color: var(--color-text-muted); margin-bottom: 1rem; }

    .article-card__btn {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        background: transparent; border: 2px solid var(--color-accent);
        color: var(--color-accent);
        font-size: 0.8125rem; font-weight: 600; letter-spacing: 0.025em;
        border-radius: var(--radius-md); text-decoration: none;
        transition: background-color 0.2s ease, color 0.2s ease, transform 0.1s ease;
        width: fit-content;
    }
    .article-card__btn:hover { background: var(--color-accent); color: var(--color-text-inv); transform: translateX(4px); }

    .articles-pagination {
        padding: 0 2rem 2.5rem; display: flex; justify-content: center;
    }
    @media (min-width: 768px) { .articles-pagination { padding: 0 3rem 3rem; } }
    .articles-pagination nav { display: flex; gap: 0.5rem; }
    .articles-pagination a, .articles-pagination span {
        padding: 0.5rem 0.75rem; border-radius: var(--radius-sm);
        font-size: 0.875rem; text-decoration: none;
        border: 1px solid var(--color-border); color: var(--color-text);
    }
    .articles-pagination .current { background: var(--color-accent); color: var(--color-text-inv); border-color: var(--color-accent); }

    /* Search form */
    .articles-search-form {
        display: flex; align-items: center; gap: 0.5rem;
        margin-top: 1.25rem; max-width: 28rem;
    }
    .articles-search-input {
        flex: 1; height: 2.25rem; padding: 0 0.875rem;
        border: 1px solid var(--color-border); border-radius: var(--radius-sm);
        background-color: var(--color-bg); color: var(--color-text);
        font-size: 0.8125rem; outline: none; transition: border-color 0.2s ease;
    }
    .articles-search-input:focus { border-color: var(--color-accent); }
    .articles-search-input::placeholder { color: var(--color-text-faint); }
    .articles-search-btn {
        display: inline-flex; align-items: center; gap: 0.375rem;
        height: 2.25rem; padding: 0 1rem;
        background: transparent; border: 1px solid var(--color-accent);
        color: var(--color-accent); font-size: 0.75rem; font-weight: 600;
        border-radius: var(--radius-sm); cursor: pointer;
        transition: background-color 0.2s ease, color 0.2s ease; white-space: nowrap;
    }
    .articles-search-btn:hover { background: var(--color-accent); color: var(--color-text-inv); }

    .articles-search-results-bar {
        display: flex; align-items: center; justify-content: space-between;
        gap: 1rem; padding: 1rem 2rem;
        background: var(--color-surface-2); border-bottom: 1px solid var(--color-border);
        font-size: 0.875rem; color: var(--color-text-muted);
    }
    @media (min-width: 768px) { .articles-search-results-bar { padding: 1rem 3rem; } }
    .articles-search-results-bar strong { color: var(--color-text); }
    .articles-search-clear {
        font-size: 0.8125rem; color: var(--color-accent);
        text-decoration: none; white-space: nowrap; transition: opacity 0.2s ease;
    }
    .articles-search-clear:hover { opacity: 0.7; }

    .articles-empty { padding: 3rem 2rem; text-align: center; color: var(--color-text-muted); }
    .articles-empty p { font-size: 1.125rem; }
</style>
@endsection

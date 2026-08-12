@extends('layouts.app')

@section('content')
<!-- Truths Revealed Blog Section -->
<section class="blog-section" aria-labelledby="blog-heading">
    <div class="blog-card">
        <!-- Section Title Bar -->
        <header class="blog-card__header">
            <div class="blog-card__title-lines">
                <span class="blog-card__line1">Project Truth Ministries &ndash; Presents</span>
                <span class="blog-card__line2">Truths Revealed Blog</span>
            </div>
            <form method="GET" action="{{ route('blog.index') }}" class="blog-search-form">
                <input type="search"
                       name="q"
                       value="{{ $search ?? '' }}"
                       placeholder="Search blogs..."
                       class="blog-search-input"
                       aria-label="Search blog posts">
                <button type="submit" class="blog-search-btn">
                    <svg class="btn-icon" aria-hidden="true"><use xlink:href="#icon-search"></use></svg>
                    Search
                </button>
            </form>
        </header>

        <!-- Blog Posts Grid -->
        @if (request('q'))
            <div class="blog-search-results-bar">
                <p>Search results for &ldquo;<strong>{{ $search }}</strong>&rdquo; — {{ $posts->total() }} post{{ $posts->total() === 1 ? '' : 's' }} found.</p>
                <a href="{{ route('blog.index') }}" class="blog-search-clear">Clear search &times;</a>
            </div>
        @endif

        @if ($posts->isNotEmpty())
            <div class="blog-grid">
                @foreach ($posts as $post)
                    <article class="blog-post">
                        @if ($post->featured_image)
                            <img src="{{ asset('images/site/' . $post->featured_image) }}"
                                 alt="{{ $post->title }}"
                                 class="blog-post__image"
                                 loading="lazy" />
                        @endif
                        <div class="blog-post__content">
                            <time class="blog-post__date" datetime="{{ $post->published_at?->toDateString() }}" style="display: block; font-size: 0.75rem; color: var(--color-accent); margin-bottom: 0.5rem;">
                                {{ $post->published_at?->format('F j, Y') ?? $post->created_at->format('F j, Y') }}
                            </time>
                            <h3 class="blog-post__title">
                                <a href="{{ route('blog.show', $post->slug) }}" style="color: inherit; text-decoration: none;">
                                    {{ $post->title }}
                                </a>
                            </h3>
                            <p class="blog-post__excerpt">
                                {{ $post->excerpt_text }}
                            </p>
                            <p class="blog-post__author" style="font-size: 0.8125rem; color: var(--color-text-muted); margin-top: 0.75rem;">
                                By {{ $post->author?->full_name ?? 'Unknown' }}
                            </p>
                            <a href="{{ route('blog.show', $post->slug) }}" class="blog-post__btn">Read Now <span aria-hidden="true">&rarr;</span></a>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($posts->hasPages())
                <div class="blog-pagination">
                    {{ $posts->links() }}
                </div>
            @endif
        @else
            <div class="blog-empty" style="padding: 3rem 2rem; text-align: center;">
                @if (request('q'))
                    <p style="color: var(--color-text-muted); font-size: 1.125rem;">No posts found for &ldquo;{{ $search }}&rdquo;.</p>
                    <a href="{{ route('blog.index') }}" class="blog-search-clear" style="display: inline-block; margin-top: 1rem;">Clear search &times;</a>
                @else
                    <p style="color: var(--color-text-muted); font-size: 1.125rem;">No blog posts have been published yet.</p>
                @endif
            </div>
        @endif
    </div>
</section>

<style>
    /* ===== Blog Index Page Styles ===== */
    .blog-section {
        padding: 0 1.5rem 4rem;
    }
    @media (min-width: 768px) {
        .blog-section {
            padding: 0 2rem 5rem;
        }
    }

    .blog-card {
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        max-width: 80rem;
        margin: 4rem auto 0;
    }

    .blog-card__header {
        background: var(--color-bg);
        border-bottom: 1px solid var(--color-border);
        padding: 2rem 2.5rem;
    }
    @media (min-width: 768px) {
        .blog-card__header {
            padding: 2.5rem 3rem;
        }
    }

    .blog-card__title-lines {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .blog-card__line1 {
        font-family: var(--font-sans);
        font-size: 0.875rem;
        font-weight: 500;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--color-accent);
    }

    .blog-card__line2 {
        font-family: var(--font-serif);
        font-size: 2rem;
        font-weight: 600;
        line-height: 1.2;
        color: var(--color-text);
    }
    @media (min-width: 768px) {
        .blog-card__line2 {
            font-size: 2.5rem;
        }
    }

    .blog-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        padding: 2rem;
    }
    @media (min-width: 768px) {
        .blog-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            padding: 2.5rem 3rem 3rem;
        }
    }
    @media (min-width: 1024px) {
        .blog-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .blog-post {
        background: var(--color-bg);
        border: 1px solid var(--color-border-soft);
        border-radius: var(--radius-md);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        display: flex;
        flex-direction: column;
    }
    .blog-post:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--color-accent);
    }

    .blog-post__image {
        width: 100%;
        aspect-ratio: 16 / 10;
        object-fit: cover;
        background: var(--color-surface-2);
    }

    .blog-post__content {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .blog-post__date {
        margin-bottom: 0.5rem;
    }

    .blog-post__title {
        font-family: var(--font-serif);
        font-size: 1.125rem;
        font-weight: 600;
        line-height: 1.4;
        margin: 0 0 0.75rem;
        color: var(--color-text);
    }

    .blog-post__excerpt {
        font-size: 0.875rem;
        line-height: 1.65;
        color: var(--color-text-muted);
        margin: 0 0 1rem;
        flex: 1;
    }

    .blog-post__author {
        margin-bottom: 1rem;
    }

    .blog-post__btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        background: transparent;
        border: 2px solid var(--color-accent);
        color: var(--color-accent);
        font-size: 0.8125rem;
        font-weight: 600;
        letter-spacing: 0.025em;
        border-radius: var(--radius-md);
        text-decoration: none;
        transition: background-color 0.2s ease, color 0.2s ease, transform 0.1s ease;
        width: fit-content;
    }
    .blog-post__btn:hover {
        background: var(--color-accent);
        color: var(--color-text-inv);
        transform: translateX(4px);
    }

    .blog-pagination {
        padding: 0 2rem 2.5rem;
        display: flex;
        justify-content: center;
    }
    @media (min-width: 768px) {
        .blog-pagination {
            padding: 0 3rem 3rem;
        }
    }
    .blog-pagination nav {
        display: flex;
        gap: 0.5rem;
    }
    .blog-pagination a, .blog-pagination span {
        padding: 0.5rem 0.75rem;
        border-radius: var(--radius-sm);
        font-size: 0.875rem;
        text-decoration: none;
        border: 1px solid var(--color-border);
        color: var(--color-text);
    }
    .blog-pagination .current {
        background: var(--color-accent);
        color: var(--color-text-inv);
        border-color: var(--color-accent);
    }

    /* Search form */
    .blog-search-form {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1.25rem;
        max-width: 28rem;
    }
    .blog-search-input {
        flex: 1;
        height: 2.25rem;
        padding: 0 0.875rem;
        border: 1px solid var(--color-border);
        border-radius: var(--radius-sm);
        background-color: var(--color-bg);
        color: var(--color-text);
        font-size: 0.8125rem;
        outline: none;
        transition: border-color 0.2s ease;
    }
    .blog-search-input:focus {
        border-color: var(--color-accent);
    }
    .blog-search-input::placeholder {
        color: var(--color-text-faint);
    }
    .blog-search-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        height: 2.25rem;
        padding: 0 1rem;
        background: transparent;
        border: 1px solid var(--color-accent);
        color: var(--color-accent);
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: background-color 0.2s ease, color 0.2s ease;
        white-space: nowrap;
    }
    .blog-search-btn:hover {
        background: var(--color-accent);
        color: var(--color-text-inv);
    }

    /* Search results bar */
    .blog-search-results-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 2rem;
        background: var(--color-surface-2);
        border-bottom: 1px solid var(--color-border);
        font-size: 0.875rem;
        color: var(--color-text-muted);
    }
    @media (min-width: 768px) {
        .blog-search-results-bar {
            padding: 1rem 3rem;
        }
    }
    .blog-search-results-bar strong {
        color: var(--color-text);
    }
    .blog-search-clear {
        font-size: 0.8125rem;
        color: var(--color-accent);
        text-decoration: none;
        white-space: nowrap;
        transition: opacity 0.2s ease;
    }
    .blog-search-clear:hover {
        opacity: 0.7;
    }
</style>
@endsection

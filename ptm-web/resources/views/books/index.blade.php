@extends('layouts.app')

@section('content')
<!-- Book Recommendations Page -->
<header class="py-12 md:py-16" style="background-color: var(--color-surface); border-bottom: 1px solid var(--color-border);">
    <div class="mx-auto max-w-7xl px-4 text-center">
        <h1 class="text-3xl md:text-4xl font-serif font-semibold" style="color: var(--color-text); margin-bottom: 0.5rem;">Book Recommendations</h1>
        <p class="text-lg max-w-2xl mx-auto" style="color: var(--color-text-muted);">
            Curated reading list for deepening your understanding of Biblical truth and history
        </p>
    </div>
</header>

<main>
    <div class="mx-auto max-w-7xl px-4 py-12">
        @if($books->count() > 0)
            <!-- Books Grid -->
            <div class="books-grid">
                @foreach ($books as $book)
                    <article class="book-card">
                        <div class="book-card__image-wrapper">
                            <img src="{{ asset('images/books/' . $book->image_front) }}"
                                 alt="{{ $book->title }}"
                                 class="book-card__image"
                                 loading="lazy" />
                            @if($book->published)
                                <span class="book-badge">Published</span>
                            @else
                                <span class="book-badge book-badge--draft">Draft</span>
                            @endif
                        </div>
                        <div class="book-card__content">
                            <h3 class="book-card__title">{{ $book->title }}</h3>
                            @if($book->subtitle)
                                <h4 class="book-card__subtitle">{{ $book->subtitle }}</h4>
                            @endif
                            <p class="book-card__author">By {{ $book->author->full_name ?? $book->author->first_name . ' ' . $book->author->last_name }}</p>
                            <p class="book-card__excerpt">{{ $book->body ? \Illuminate\Support\Str::limit(strip_tags($book->body), 120) : 'No description available.' }}</p>
                            
                            <div class="book-card__meta">
                                @if($book->published_at)
                                    <time class="book-card__date" datetime="{{ $book->published_at->format('Y-m-d') }}">
                                        {{ $book->published_at->format('F Y') }}
                                    </time>
                                @endif
                                @if($book->page_count)
                                    <span class="book-card__pages">{{ $book->page_count }} pages</span>
                                @endif
                            </div>
                            
                            <div class="book-card__actions">
                                @if($book->amazon_link)
                                    <a href="{{ $book->amazon_link }}" target="_blank" rel="noopener noreferrer" class="book-btn book-btn--amazon">
                                        <svg class="btn-icon" aria-hidden="true"><use xlink:href="#icon-amazon"></use></svg>
                                        Amazon
                                    </a>
                                @endif
                                @if($book->lulu_link)
                                    <a href="{{ $book->lulu_link }}" target="_blank" rel="noopener noreferrer" class="book-btn book-btn--lulu">
                                        <svg class="btn-icon" aria-hidden="true"><use xlink:href="#icon-book"></use></svg>
                                        Lulu
                                    </a>
                                @endif
                                <a href="{{ route('books.show', $book->slug) }}" class="book-btn book-btn--details">
                                    <svg class="btn-icon" aria-hidden="true"><use xlink:href="#icon-search"></use></svg>
                                    Details
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            {{ $books->links('pagination::tailwind') }}
        @else
            <div class="text-center py-12" style="color: var(--color-text-muted);">
                <p class="text-lg">No published books available at this time.</p>
            </div>
        @endif
    </div>
</main>

<style>
    /* ===== Books Index Page Styles ===== */
    .books-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
        max-width: 80rem;
        margin: 0 auto;
    }
    @media (min-width: 640px) {
        .books-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (min-width: 1024px) {
        .books-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .book-card {
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        display: flex;
        flex-direction: column;
    }
    .book-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--color-accent);
    }

    .book-card__image-wrapper {
        position: relative;
        aspect-ratio: 2 / 3;
        background: var(--color-surface-2);
        overflow: hidden;
    }
    .book-card__image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .book-card:hover .book-card__image {
        transform: scale(1.03);
    }

    .book-badge {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        padding: 0.25rem 0.75rem;
        font-size: 0.625rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-radius: 9999px;
        background-color: var(--color-accent);
        color: var(--color-text-inv);
    }
    .book-badge--draft {
        background-color: var(--color-text-muted);
    }

    .book-card__content {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .book-card__title {
        font-family: var(--font-serif);
        font-size: 1.125rem;
        font-weight: 600;
        line-height: 1.35;
        margin: 0 0 0.375rem;
        color: var(--color-text);
    }

    .book-card__subtitle {
        font-family: var(--font-serif);
        font-size: 0.875rem;
        font-weight: 400;
        font-style: italic;
        color: var(--color-text-muted);
        margin: 0 0 0.5rem;
    }

    .book-card__author {
        font-size: 0.8125rem;
        font-weight: 500;
        color: var(--color-accent);
        margin: 0 0 0.75rem;
    }

    .book-card__excerpt {
        font-size: 0.875rem;
        line-height: 1.6;
        color: var(--color-text-muted);
        margin: 0 0 1rem;
        flex: 1;
    }

    .book-card__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
        font-size: 0.75rem;
        color: var(--color-text-muted);
    }

    .book-card__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .book-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: var(--radius-md);
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-icon {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
    }

    .book-btn--amazon {
        background-color: #ff9900;
        color: #111;
    }
    .book-btn--amazon:hover {
        background-color: #e68a00;
    }

    .book-btn--lulu {
        background-color: var(--color-surface);
        color: var(--color-text);
        border: 1px solid var(--color-border);
    }
    .book-btn--lulu:hover {
        border-color: var(--color-accent);
        color: var(--color-accent);
    }

    .book-btn--details {
        background-color: var(--color-accent);
        color: var(--color-text-inv);
    }
    .book-btn--details:hover {
        background-color: var(--color-accent-hi);
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 2rem;
    }
    .pagination a,
    .pagination span {
        padding: 0.5rem 1rem;
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    .pagination a {
        background-color: var(--color-surface);
        border: 1px solid var(--color-border);
        color: var(--color-text);
    }
    .pagination a:hover {
        border-color: var(--color-accent);
        color: var(--color-accent);
    }
    .pagination span[aria-current="page"] {
        background-color: var(--color-accent);
        border-color: var(--color-accent);
        color: var(--color-text-inv);
    }
</style>
@endsection

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
        </header>

        <!-- Blog Posts Grid -->
        <div class="blog-grid">
            @foreach ($posts as $post)
                <!-- Post -->
                <article class="blog-post">
                    <img src="{{ asset('images/site/' . $post['image']) }}"
                         alt=""
                         class="blog-post__image"
                         loading="lazy" />
                    <div class="blog-post__content">
                        <time class="blog-post__date" datetime="{{ $post['date'] }}" style="display: block; font-size: 0.75rem; color: var(--color-accent); margin-bottom: 0.5rem;">
                            {{ \Carbon\Carbon::parse($post['date'])->format('F j, Y') }}
                        </time>
                        <h3 class="blog-post__title">{{ $post['title'] }}</h3>
                        <p class="blog-post__excerpt">
                            {{ $post['excerpt'] }}
                        </p>
                        <p class="blog-post__author" style="font-size: 0.8125rem; color: var(--color-text-muted); margin-top: 0.75rem;">
                            By {{ $post['author'] }}
                        </p>
                        <a href="#" class="blog-post__btn">Read Now <span aria-hidden="true">&rarr;</span></a>
                    </div>
                </article>
            @endforeach
        </div>
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
</style>
@endsection
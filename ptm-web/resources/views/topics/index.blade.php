@extends('layouts.app')

@section('content')
<!-- Truth Topics Section -->
<section class="topics-section" aria-labelledby="topics-heading">
    <div class="topics-card">
        <!-- Section Title Bar -->
        <header class="topics-card__header">
            <div class="topics-card__title-lines">
                <span class="topics-card__line1">Project Truth Ministries &ndash; Presents</span>
                <span class="topics-card__line2">Truth Topics</span>
            </div>
        </header>

        <!-- Topics Grid -->
        <div class="topics-grid">
            @foreach ($topics as $topic)
                <!-- Topic Card -->
                <article class="topic-post">
                    <img src="{{ asset('images/site/' . $topic['image']) }}"
                         alt=""
                         class="topic-post__image"
                         loading="lazy" />
                    <div class="topic-post__content">
                        <h3 class="topic-post__title">{{ $topic['title'] }}</h3>
                        <p class="topic-post__excerpt">
                            {{ $topic['excerpt'] }}
                        </p>
                        <a href="#" class="topic-post__btn">Read Now <span aria-hidden="true">&rarr;</span></a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<style>
    /* ===== Truth Topics Page Styles ===== */
    .topics-section {
        padding: 0 1.5rem 4rem;
    }
    @media (min-width: 768px) {
        .topics-section {
            padding: 0 2rem 5rem;
        }
    }

    .topics-card {
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        max-width: 80rem;
        margin: 4rem auto 0;
    }

    .topics-card__header {
        background: var(--color-bg);
        border-bottom: 1px solid var(--color-border);
        padding: 2rem 2.5rem;
    }
    @media (min-width: 768px) {
        .topics-card__header {
            padding: 2.5rem 3rem;
        }
    }

    .topics-card__title-lines {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .topics-card__line1 {
        font-family: var(--font-sans);
        font-size: 0.875rem;
        font-weight: 500;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--color-accent);
    }

    .topics-card__line2 {
        font-family: var(--font-serif);
        font-size: 2rem;
        font-weight: 600;
        line-height: 1.2;
        color: var(--color-text);
    }
    @media (min-width: 768px) {
        .topics-card__line2 {
            font-size: 2.5rem;
        }
    }

    .topics-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        padding: 2rem;
    }
    @media (min-width: 640px) {
        .topics-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (min-width: 768px) {
        .topics-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            padding: 2.5rem 3rem 3rem;
        }
    }
    @media (min-width: 1024px) {
        .topics-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    .topic-post {
        background: var(--color-bg);
        border: 1px solid var(--color-border-soft);
        border-radius: var(--radius-md);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        display: flex;
        flex-direction: column;
    }
    .topic-post:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--color-accent);
    }

    .topic-post__image {
        width: 100%;
        aspect-ratio: 16 / 10;
        object-fit: cover;
        background: var(--color-surface-2);
    }

    .topic-post__content {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .topic-post__title {
        font-family: var(--font-serif);
        font-size: 1.125rem;
        font-weight: 600;
        line-height: 1.4;
        margin: 0 0 0.75rem;
        color: var(--color-text);
    }

    .topic-post__excerpt {
        font-size: 0.875rem;
        line-height: 1.65;
        color: var(--color-text-muted);
        margin: 0 0 1.5rem;
        flex: 1;
    }

    .topic-post__btn {
        display: inline-flex;
        align-items: center
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
    .topic-post__btn:hover {
        background: var(--color-accent);
        color: var(--color-text-inv);
        transform: translateX(4px);
    }
</style>
@endsection
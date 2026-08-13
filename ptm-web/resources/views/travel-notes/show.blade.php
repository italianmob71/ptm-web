@extends('layouts.app')

@section('content')
<article class="tn-article">
    <!-- Header -->
    <header class="tn-article__header">
        <div class="tn-article__header-inner">
            <a href="{{ route('travel-notes.index') }}" class="tn-article__back">
                <span aria-hidden="true">&larr;</span> All Travel Notes
            </a>
            <h1 class="tn-article__title">{{ $note->title }}</h1>
            <div class="tn-article__meta">
                @if ($note->biblical_reference)
                    <span class="tn-article__ref">{{ $note->biblical_reference }}</span>
                    <span class="tn-article__sep">&middot;</span>
                @endif
                @if ($note->location)
                    <span>{{ $note->location }}</span>
                    <span class="tn-article__sep">&middot;</span>
                @endif
                <time>{{ $note->published_at?->format('F j, Y') ?? $note->created_at->format('F j, Y') }}</time>
            </div>
        </div>
    </header>

    <!-- Teaser image (if set) -->
    @if ($note->teaserImage)
    <div class="tn-article__hero">
        <img src="{{ $note->teaserImage->url }}" alt="{{ $note->title }}">
    </div>
    @endif

    <!-- Body -->
    <div class="tn-article__body prose-scholarly">
        {!! $note->content !!}
    </div>

    <!-- About the Author -->
    @if ($note->author)
    <section class="tn-author">
        <div class="tn-author__inner">
            <h2 class="tn-author__heading">About the Author</h2>
            <div class="tn-author__content">
                @if ($note->author->image)
                    <img src="{{ asset('images/authors/' . $note->author->image) }}"
                         alt="{{ $note->author->full_name }}"
                         class="tn-author__avatar">
                @else
                    <div class="tn-author__avatar tn-author__avatar--placeholder">
                        <span>{{ strtoupper(substr($note->author->full_name, 0, 1)) }}</span>
                    </div>
                @endif
                <div class="tn-author__info">
                    <h3 class="tn-author__name">{{ $note->author->full_name }}</h3>
                    @if ($note->author->title)
                        <p class="tn-author__role">{{ $note->author->title }}</p>
                    @endif
                    @if ($note->author->bio)
                        <div class="tn-author__bio">{!! $note->author->bio !!}</div>
                    @endif
                    @if ($note->author->social_profiles)
                        <div class="tn-author__socials">
                            <span class="tn-author__socials-label">Socials</span>
                            <div class="tn-author__socials-row">
                                @foreach ($note->author->social_profiles as $social)
                                    <a href="{{ $social['url'] }}"
                                       target="_blank" rel="noopener noreferrer"
                                       class="tn-author-social-link"
                                       title="{{ ucfirst($social['platform']) }}"
                                       aria-label="{{ $note->author->full_name }} on {{ ucfirst($social['platform']) }}">
                                        @if ($social['platform'] === 'x')
                                            <svg class="tn-author-social-icon" viewBox="0 0 1200 1227" aria-hidden="true">
                                                <path fill="currentColor" d="M714.163 519.288L1170.63 0H1049.23L653.496 450.808L323.56 0H0L479.986 682.528L0 1227H121.403L539.514 750.884L853.683 1227H1226.24L714.133 519.234zM546.322 685.061L493.446 608.375L158.47 78.236H259.11L593.437 542.751L646.314 619.437L989.283 1151.01H888.64L546.322 685.061z"/>
                                            </svg>
                                        @else
                                            <svg class="tn-author-social-icon" aria-hidden="true"><use xlink:href="#{{ $social['icon'] }}"></use></svg>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    @endif

    @if ($related->isNotEmpty())
    <!-- Related -->
    <section class="tn-related">
        <h2 class="tn-related__heading">More Travel Notes</h2>
        <div class="tn-related__grid">
            @foreach ($related as $rel)
                <a href="{{ route('travel-notes.show', $rel->slug) }}" class="tn-related__card">
                    @if ($rel->teaserImage)
                        <div class="tn-related__thumb">
                            <img src="{{ $rel->teaserImage->url }}" alt="{{ $rel->title }}" loading="lazy">
                        </div>
                    @endif
                    <div class="tn-related__content">
                        @if ($rel->biblical_reference)
                            <span class="tn-related__ref">{{ $rel->biblical_reference }}</span>
                        @endif
                        <h3 class="tn-related__title">{{ $rel->title }}</h3>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @endif
</article>

<style>
    .tn-article { max-width: 48rem; margin: 0 auto; padding: 2rem 1.5rem 4rem; }
    @media (min-width: 768px) { .tn-article { padding: 3rem 2rem 5rem; } }

    .tn-article__header { margin-bottom: 2.5rem; }
    .tn-article__back {
        display: inline-block; font-size: 0.8125rem; font-weight: 600;
        color: var(--color-accent); text-decoration: none;
        margin-bottom: 1.5rem; transition: opacity 0.2s ease;
    }
    .tn-article__back:hover { opacity: 0.7; }

    .tn-article__title {
        font-family: var(--font-serif); font-size: 1.875rem; font-weight: 700;
        line-height: 1.25; color: var(--color-text); margin: 0 0 1rem;
    }
    @media (min-width: 768px) { .tn-article__title { font-size: 2.25rem; } }

    .tn-article__meta { font-size: 0.875rem; color: var(--color-text-muted); display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem; }
    .tn-article__ref { font-weight: 600; color: var(--color-accent); }
    .tn-article__sep { color: var(--color-text-faint); }

    /* Teaser hero image */
    .tn-article__hero { margin: 0 0 2.5rem; border-radius: var(--radius-md); overflow: hidden; }
    .tn-article__hero img { width: 100%; height: auto; display: block; max-height: 24rem; object-fit: cover; }

    .tn-article__body { margin-bottom: 3rem; }
    .tn-article__body p { margin: 1rem 0; line-height: 1.8; }
    .tn-article__body h2 { font-family: var(--font-serif); font-size: 1.5rem; font-weight: 600; margin: 2rem 0 0.75rem; color: var(--color-text); }
    .tn-article__body h3 { font-family: var(--font-serif); font-size: 1.25rem; font-weight: 600; margin: 1.5rem 0 0.5rem; color: var(--color-text); }
    .tn-article__body blockquote {
        border-left: 4px solid var(--color-accent);
        padding: 0.5rem 1rem; margin: 1rem 0;
        font-style: italic; color: var(--color-text-muted);
        background: var(--color-surface-2);
        border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
    }
    .tn-article__body blockquote p { margin: 0.25rem 0; }
    .tn-article__body a { color: var(--color-accent); }
    .tn-article__body img { max-width: 100%; height: auto; border-radius: var(--radius-md); margin: 1.5rem 0; }
    /* Images in tables */
    .tn-article__body td img { max-width: 100% !important; height: auto !important; }
    .tn-article__body table { table-layout: auto; }
    .tn-article__body td, .tn-article__body th { border: 1px solid var(--color-border); padding: 0.5rem; }

    /* Author card */
    .tn-author { margin: 3rem 0; border-top: 1px solid var(--color-border); padding-top: 2.5rem; }
    .tn-author__heading { text-align: center; font-family: var(--font-serif); font-size: 1.25rem; font-weight: 600; color: var(--color-text-muted); margin: 0 0 1.5rem; }
    .tn-author__content { display: flex; flex-direction: column; align-items: center; gap: 1.5rem; }
    @media (min-width: 640px) { .tn-author__content { flex-direction: row; align-items: flex-start; } }
    .tn-author__avatar {
        width: 5rem; height: 5rem; border-radius: 50%; border: 2px solid var(--color-border);
        object-fit: cover; flex-shrink: 0;
    }
    .tn-author__avatar--placeholder {
        background: var(--color-surface-2); display: flex; align-items: center; justify-content: center;
        font-family: var(--font-serif); font-size: 1.5rem; font-weight: 700; color: var(--color-text-muted);
    }
    .tn-author__info { flex: 1; text-align: center; }
    @media (min-width: 640px) { .tn-author__info { text-align: left; } }
    .tn-author__name { font-family: var(--font-serif); font-size: 1.125rem; font-weight: 700; color: var(--color-text); margin: 0 0 0.25rem; }
    .tn-author__role { font-size: 0.8125rem; color: var(--color-accent); margin: 0 0 0.75rem; }
    .tn-author__bio { font-size: 0.875rem; line-height: 1.65; color: var(--color-text-muted); }

    /* Author socials */
    .tn-author__socials { margin-top: 1rem; }
    .tn-author__socials-label {
        display: inline-block; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
        letter-spacing: 0.05em; color: var(--color-text-faint); margin-right: 0.5rem;
    }
    .tn-author__socials-row { display: inline-flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap; }
    @media (min-width: 640px) { .tn-author__socials-row { justify-content: flex-start; } }
    .tn-author-social-link {
        display: inline-flex; align-items: center; justify-content: center;
        width: 2rem; height: 2rem; border-radius: 50%;
        background: var(--color-surface-2); color: var(--color-text-muted);
        transition: background-color 0.2s ease, color 0.2s ease, transform 0.1s ease;
        text-decoration: none;
    }
    .tn-author-social-link:hover { background: var(--color-accent); color: var(--color-text-inv); transform: translateY(-2px); }
    .tn-author-social-icon { width: 1rem; height: 1rem; }

    /* Related */
    .tn-related { margin-top: 3rem; border-top: 1px solid var(--color-border); padding-top: 2.5rem; }
    .tn-related__heading { font-family: var(--font-serif); font-size: 1.25rem; font-weight: 600; color: var(--color-text); margin: 0 0 1.5rem; }
    .tn-related__grid { display: grid; grid-template-columns: 1fr; gap: 1rem; }
    @media (min-width: 640px) { .tn-related__grid { grid-template-columns: repeat(3, 1fr); } }
    .tn-related__card {
        display: block; overflow: hidden;
        border: 1px solid var(--color-border-soft); border-radius: var(--radius-md);
        text-decoration: none; transition: transform 0.2s ease, border-color 0.2s ease;
    }
    .tn-related__card:hover { transform: translateY(-2px); border-color: var(--color-accent); }
    .tn-related__thumb { aspect-ratio: 16/10; overflow: hidden; background: var(--color-surface-2); }
    .tn-related__thumb img { width: 100%; height: 100%; object-fit: cover; }
    .tn-related__content { padding: 1rem; }
    .tn-related__ref { font-size: 0.75rem; font-weight: 600; color: var(--color-accent); display: block; margin-bottom: 0.25rem; }
    .tn-related__title { font-family: var(--font-serif); font-size: 1rem; font-weight: 600; color: var(--color-text); margin: 0; line-height: 1.4; }
</style>
@endsection

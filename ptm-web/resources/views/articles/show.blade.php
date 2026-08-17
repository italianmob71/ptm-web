@extends('layouts.app')

@section('content')
<article class="scholarly-article" itemscope itemtype="https://schema.org/ScholarlyArticle">
    <!-- Article header -->
    <header class="s-article-header">
        <div class="s-article-header__inner">
            <a href="{{ route('articles.index') }}" class="s-back-link">
                <span aria-hidden="true">&larr;</span> All Articles
            </a>
            <h1 class="s-article-title" itemprop="headline">{{ $article->title }}</h1>
            @if ($article->sub_title)
                <p class="s-article-subtitle">{{ $article->sub_title }}</p>
            @endif
            <div class="s-article-meta">
                <span class="s-article-meta__author" itemprop="author">
                    By <strong>{{ $article->author?->full_name ?? 'Unknown' }}</strong>
                </span>
                <span class="s-article-meta__sep">&middot;</span>
                <time itemprop="datePublished" datetime="{{ $article->published_at?->toDateString() }}">
                    {{ $article->published_at?->format('F j, Y') ?? $article->created_at->format('F j, Y') }}
                </time>
            </div>
        </div>
    </header>

    <!-- Article body -->
    <div class="s-article-body prose-scholarly" itemprop="articleBody">
        {!! $article->content !!}
    </div>

    @if ($article->author)
    <!-- About the author -->
    <section class="s-author-card">
        <div class="s-author-card__inner">
            <h2 class="s-author-card__heading">About the Author</h2>
            <div class="s-author-card__content">
                @if ($article->author->image)
                    <img src="{{ asset('images/authors/' . $article->author->image) }}"
                         alt="{{ $article->author->full_name }}"
                         class="s-author-card__avatar"
                         loading="lazy" />
                @endif
                <div class="s-author-card__info">
                    <h3 class="s-author-card__name">{{ $article->author->full_name }}</h3>
                    @if ($article->author->title)
                        <p class="s-author-card__title">{{ $article->author->title }}</p>
                    @endif
                    @if ($article->author->bio)
                        <div class="s-author-card__bio">{!! $article->author->bio !!}</div>
                    @endif
                    @if ($article->author->social_profiles)
                        <div class="s-author-card__socials">
                            <span class="s-author-card__socials-label">Socials</span>
                            <div class="s-author-card__socials-row">
                                @foreach ($article->author->social_profiles as $social)
                                    <a href="{{ $social['url'] }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="s-author-social-link"
                                       title="{{ ucfirst($social['platform']) }}"
                                       aria-label="{{ $article->author->full_name }} on {{ ucfirst($social['platform']) }}">
                                        @if ($social['platform'] === 'x')
                                            <svg class="s-author-social-icon" viewBox="0 0 1200 1227" aria-hidden="true">
                                                <path fill="currentColor" d="M714.163 519.284L1160.89 0H1055.03L667.137 450.887L357.328 0H0L468.492 681.821L0 1226.37H105.866L515.491 750.218L842.672 1226.37H1200L714.137 519.284H714.163ZM569.165 687.828L521.697 619.934L144.011 79.6944H306.615L611.412 515.685L658.88 583.579L1055.08 1150.3H892.476L569.165 687.854V687.828Z"/>
                                            </svg>
                                        @else
                                            <svg class="s-author-social-icon" aria-hidden="true">
                                                <use xlink:href="#{{ $social['icon'] }}"></use>
                                            </svg>
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
    <!-- Related articles -->
    <section class="s-related">
        <h2 class="s-related__heading">More Articles</h2>
        <div class="s-related__grid">
            @foreach ($related as $rel)
                <a href="{{ route('articles.show', $rel->slug) }}" class="s-related__card">
                    <time class="s-related__date">{{ $rel->published_at?->format('M j, Y') ?? $rel->created_at->format('M j, Y') }}</time>
                    <h3 class="s-related__title">{{ $rel->title }}</h3>
                    <p class="s-related__author">{{ $rel->author?->full_name ?? 'Unknown' }}</p>
                </a>
            @endforeach
        </div>
    </section>
    @endif
</article>

<style>
    /* ===== Article Show — mirrors Blog show ===== */
    .scholarly-article { max-width: 48rem; margin: 0 auto; padding: 2rem 1.5rem 4rem; }
    @media (min-width: 768px) { .scholarly-article { padding: 3rem 2rem 5rem; } }

    .s-article-header { margin-bottom: 2.5rem; }
    .s-back-link {
        display: inline-block; font-size: 0.8125rem; font-weight: 600;
        color: var(--color-accent); text-decoration: none;
        margin-bottom: 1.5rem; transition: opacity 0.2s ease;
    }
    .s-back-link:hover { opacity: 0.7; }

    .s-article-title {
        font-family: var(--font-serif);
        font-size: 1.875rem; font-weight: 700; line-height: 1.25;
        color: var(--color-text); margin: 0 0 1rem;
    }
    @media (min-width: 768px) { .s-article-title { font-size: 2.25rem; } }

    .s-article-subtitle {
        font-family: var(--font-serif);
        font-size: 1.125rem; font-weight: 400; line-height: 1.5;
        color: var(--color-text-muted); margin: -0.5rem 0 1rem 0;
        font-style: italic;
    }

    .s-article-meta {
        font-size: 0.875rem; color: var(--color-text-muted);
        display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem;
    }
    .s-article-meta__author strong { color: var(--color-text); }
    .s-article-meta__sep { color: var(--color-text-faint); }

    .s-article-body { margin-bottom: 3rem; }
    .s-article-body p { margin: 1rem 0; line-height: 1.8; }
    .s-article-body h2 { font-family: var(--font-serif); font-size: 1.5rem; font-weight: 600; margin: 2rem 0 0.75rem; color: var(--color-text); }
    .s-article-body h3 { font-family: var(--font-serif); font-size: 1.25rem; font-weight: 600; margin: 1.5rem 0 0.5rem; color: var(--color-text); }
    .s-article-body blockquote {
        border-left: 4px solid var(--color-accent);
        padding: 0.5rem 1rem; margin: 1rem 0;
        font-style: italic; color: var(--color-text-muted);
        background: var(--color-surface-2);
        border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
    }
    .s-article-body blockquote p { margin: 0.25rem 0; }
    .s-article-body a { color: var(--color-accent); }
    .s-article-body img { max-width: 100%; height: auto; border-radius: var(--radius-md); margin: 1.5rem 0; }
/* Images in tables — shrink to fit cell */
.s-article-body td img { max-width: 100% !important; height: auto !important; }
.s-article-body table { table-layout: auto; }

    /* Author card */
    .s-author-card { margin: 3rem 0; border-top: 1px solid var(--color-border); padding-top: 2.5rem; }
    .s-author-card__heading {
        text-align: center; font-family: var(--font-serif);
        font-size: 1.25rem; font-weight: 600; color: var(--color-text-muted);
        margin: 0 0 1.5rem;
    }
    .s-author-card__content { display: flex; flex-direction: column; align-items: center; gap: 1.5rem; }
    @media (min-width: 640px) { .s-author-card__content { flex-direction: row; align-items: flex-start; } }
    .s-author-card__avatar {
        width: 5rem; height: 5rem; border-radius: 50%;
        object-fit: cover; flex-shrink: 0;
        border: 2px solid var(--color-border);
    }
    @media (min-width: 640px) { .s-author-card__avatar { width: 6rem; height: 6rem; } }
    .s-author-card__info { flex: 1; text-align: center; }
    @media (min-width: 640px) { .s-author-card__info { text-align: left; } }
    .s-author-card__name { font-family: var(--font-serif); font-size: 1.125rem; font-weight: 700; color: var(--color-text); margin: 0 0 0.25rem; }
    .s-author-card__title { font-size: 0.8125rem; color: var(--color-accent); margin: 0 0 0.75rem; }
    .s-author-card__bio { font-size: 0.875rem; line-height: 1.65; color: var(--color-text-muted); }

    .s-author-card__socials { margin-top: 1rem; }
    .s-author-card__socials-label {
        display: block; font-size: 0.6875rem; font-weight: 600;
        letter-spacing: 0.08em; text-transform: uppercase;
        color: var(--color-text-faint); margin-bottom: 0.5rem;
    }
    @media (min-width: 640px) { .s-author-card__socials-label { text-align: left; } }
    .s-author-card__socials-row { display: flex; gap: 0.75rem; justify-content: center; }
    @media (min-width: 640px) { .s-author-card__socials-row { justify-content: flex-start; } }
    .s-author-social-link {
        display: inline-flex; align-items: center; justify-content: center;
        width: 2.5rem; height: 2.5rem; border-radius: 50%;
        border: 1px solid var(--color-border); color: var(--color-text-muted);
        transition: transform 0.2s ease, border-color 0.2s ease, background-color 0.2s ease, color 0.2s ease;
    }
    .s-author-social-link:hover { transform: translateY(-2px); border-color: var(--color-accent); background-color: var(--color-accent); color: var(--color-text-inv); }
    .s-author-social-icon { width: 1.25rem; height: 1.25rem; display: block; pointer-events: none; }
    .s-author-social-icon use { pointer-events: none; }

    /* Related */
    .s-related { margin-top: 3rem; border-top: 1px solid var(--color-border); padding-top: 2.5rem; }
    .s-related__heading { font-family: var(--font-serif); font-size: 1.25rem; font-weight: 600; color: var(--color-text); margin: 0 0 1.5rem; }
    .s-related__grid { display: grid; grid-template-columns: 1fr; gap: 1rem; }
    @media (min-width: 640px) { .s-related__grid { grid-template-columns: repeat(3, 1fr); } }
    .s-related__card {
        display: block; padding: 1.25rem;
        background: var(--color-surface); border: 1px solid var(--color-border-soft);
        border-radius: var(--radius-md); text-decoration: none;
        transition: transform 0.2s ease, border-color 0.2s ease;
    }
    .s-related__card:hover { transform: translateY(-2px); border-color: var(--color-accent); }
    .s-related__date { display: block; font-size: 0.75rem; color: var(--color-accent); margin-bottom: 0.5rem; }
    .s-related__title { font-family: var(--font-serif); font-size: 1rem; font-weight: 600; color: var(--color-text); margin: 0 0 0.5rem; line-height: 1.4; }
    .s-related__author { font-size: 0.8125rem; color: var(--color-text-muted); }
</style>
@endsection

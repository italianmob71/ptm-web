@extends('layouts.app')

@section('content')
<article class="blog-article" itemscope itemtype="https://schema.org/Article">
    <!-- Article header -->
    <header class="article-header">
        <div class="article-header__inner">
            <a href="{{ route('blog.index') }}" class="back-link">
                <span aria-hidden="true">&larr;</span> All Posts
            </a>
            <h1 class="article-title" itemprop="headline">{{ $post->title }}</h1>
            <div class="article-meta">
                <span class="article-meta__author" itemprop="author">
                    By <strong>{{ $post->author?->full_name ?? 'Unknown' }}</strong>
                </span>
                <span class="article-meta__sep">&middot;</span>
                <time itemprop="datePublished" datetime="{{ $post->published_at?->toDateString() }}">
                    {{ $post->published_at?->format('F j, Y') ?? $post->created_at->format('F j, Y') }}
                </time>
                <span class="article-meta__sep">&middot;</span>
                <span>{{ $post->read_time }} min read</span>
            </div>
        </div>
    </header>

    <!-- Featured image -->
    @if ($post->featured_image)
        <div class="article-featured-image">
            <img src="{{ asset('images/site/' . $post->featured_image) }}"
                 alt="{{ $post->title }}"
                 loading="eager" />
        </div>
    @endif

    <!-- Article body -->
    <div class="article-body prose-scholarly" itemprop="articleBody">
        {!! $post->content !!}
    </div>

    {{-- See Also: auto-populated by slug relevancy --}}
    <x-see-also :source="$post" :limit="6" />

    @if ($post->author)
    <!-- About the author -->
    <section class="author-card">
        <div class="author-card__inner">
            <h2 class="author-card__heading">About the Author</h2>
            <div class="author-card__content">
                @if ($post->author->image)
                    <img src="{{ asset('images/authors/' . $post->author->image) }}"
                         alt="{{ $post->author->full_name }}"
                         class="author-card__avatar"
                         loading="lazy" />
                @endif
                <div class="author-card__info">
                    <h3 class="author-card__name">{{ $post->author->full_name }}</h3>
                    @if ($post->author->title)
                        <p class="author-card__title">{{ $post->author->title }}</p>
                    @endif
                    @if ($post->author->bio)
                        <div class="author-card__bio">{!! $post->author->bio !!}</div>
                    @endif
                    @if ($post->author->social_profiles)
                        <div class="author-card__socials">
                            <span class="author-card__socials-label">Socials</span>
                            <div class="author-card__socials-row">
                                @foreach ($post->author->social_profiles as $social)
                                    <a href="{{ $social['url'] }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="author-social-link"
                                       title="{{ ucfirst($social['platform']) }}"
                                       aria-label="{{ $post->author->full_name }} on {{ ucfirst($social['platform']) }}">
                                        @if ($social['platform'] === 'x')
                                            <svg class="author-social-icon" viewBox="0 0 1200 1227" aria-hidden="true">
                                                <path fill="currentColor" d="M714.163 519.284L1160.89 0H1055.03L667.137 450.887L357.328 0H0L468.492 681.821L0 1226.37H105.866L515.491 750.218L842.672 1226.37H1200L714.137 519.284H714.163ZM569.165 687.828L521.697 619.934L144.011 79.6944H306.615L611.412 515.685L658.88 583.579L1055.08 1150.3H892.476L569.165 687.854V687.828Z"/>
                                            </svg>
                                        @else
                                            <svg class="author-social-icon" aria-hidden="true">
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

    <!-- Related posts -->
    @if ($related->isNotEmpty())
        <section class="related-posts">
            <h2 class="related-posts__heading">More From the Blog</h2>
            <div class="related-posts__grid">
                @foreach ($related as $rel)
                    <a href="{{ route('blog.show', $rel->slug) }}" class="related-post">
                        <time class="related-post__date">{{ $rel->published_at?->format('M j, Y') ?? $rel->created_at->format('M j, Y') }}</time>
                        <h3 class="related-post__title">{{ $rel->title }}</h3>
                        <p class="related-post__author">By {{ $rel->author?->full_name ?? 'Unknown' }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</article>

<style>
    .article-header {
        background: var(--color-surface);
        border-bottom: 1px solid var(--color-border);
        padding: 3rem 1.5rem 2.5rem;
    }
    @media (min-width: 768px) {
        .article-header {
            padding: 4rem 2rem 3rem;
        }
    }

    .article-header__inner {
        max-width: 50rem;
        margin: 0 auto;
    }

    .back-link {
        display: inline-block;
        font-size: 0.875rem;
        color: var(--color-text-muted);
        text-decoration: none;
        margin-bottom: 1.5rem;
        transition: color 0.2s ease;
    }
    .back-link:hover {
        color: var(--color-accent);
    }

    .article-title {
        font-family: var(--font-serif);
        font-size: 2rem;
        line-height: 1.2;
        font-weight: 600;
        color: var(--color-text);
        margin: 0 0 1rem;
    }
    @media (min-width: 768px) {
        .article-title {
            font-size: 2.75rem;
        }
    }

    .article-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: var(--color-text-muted);
    }
    .article-meta__sep {
        color: var(--color-text-faint);
    }
    .article-meta__author strong {
        color: var(--color-text);
    }

    .article-featured-image {
        width: 100%;
        max-height: 28rem;
        overflow: hidden;
    }
    .article-featured-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .article-body {
        max-width: 50rem;
        margin: 0 auto;
        padding: 3rem 1.5rem;
        color: var(--color-text);
        line-height: 1.8;
    }
    @media (min-width: 768px) {
        .article-body {
            padding: 4rem 2rem;
        }
    }

    .article-body h1 {
        font-family: var(--font-serif);
        font-size: 1.875rem;
        font-weight: 600;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
        color: var(--color-text);
    }
    .article-body h2 {
        font-family: var(--font-serif);
        font-size: 1.5rem;
        font-weight: 600;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
        color: var(--color-text);
    }
    .article-body h3 {
        font-family: var(--font-serif);
        font-size: 1.25rem;
        font-weight: 600;
        margin-top: 2rem;
        margin-bottom: 0.75rem;
        color: var(--color-text);
    }
    .article-body p {
        margin-bottom: 1.25rem;
        font-size: 1rem;
        line-height: 1.8;
    }
    .article-body a {
        color: var(--color-accent);
        text-decoration: underline;
    }
    .article-body img {
        max-width: 100%;
        height: auto;
        margin: 1.5rem 0;
        border-radius: var(--radius-md);
    }
    /* Images in tables — shrink to fit cell */
    .article-body td img { max-width: 100% !important; height: auto !important; }
    .article-body table { table-layout: auto; }
    .article-body blockquote {
        border-left: 4px solid var(--color-accent);
        padding-left: 1.5rem;
        margin: 1.5rem 0;
        font-style: italic;
        color: var(--color-text-muted);
    }
    .article-body ul, .article-body ol {
        padding-left: 1.5rem;
        margin-bottom: 1.25rem;
    }
    .article-body li {
        margin-bottom: 0.5rem;
        line-height: 1.7;
    }
    .article-body pre {
        background: var(--color-surface-2);
        padding: 1rem;
        border-radius: var(--radius-md);
        overflow-x: auto;
        margin: 1.5rem 0;
    }
    .article-body code {
        font-family: var(--font-mono, monospace);
        font-size: 0.875em;
    }
    .article-body hr {
        border: none;
        border-top: 1px solid var(--color-border);
        margin: 2.5rem 0;
    }
    .article-body table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.5rem 0;
    }
    .article-body th, .article-body td {
        border: 1px solid var(--color-border);
        padding: 0.75rem;
    }
    .article-body th {
        background: var(--color-surface-2);
    }

    /* About the author card */
    .author-card {
        background: var(--color-surface);
        border-top: 1px solid var(--color-border);
        padding: 3rem 1.5rem;
    }
    @media (min-width: 768px) {
        .author-card {
            padding: 4rem 2rem;
        }
    }

    .author-card__inner {
        max-width: 50rem;
        margin: 0 auto;
    }

    .author-card__heading {
        font-family: var(--font-serif);
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--color-text);
        margin: 0 0 1.5rem;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .author-card__content {
        display: flex;
        gap: 1.5rem;
        align-items: flex-start;
    }

    .author-card__avatar {
        flex-shrink: 0;
        width: 5rem;
        height: 5rem;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--color-border);
    }
    @media (min-width: 640px) {
        .author-card__avatar {
            width: 6rem;
            height: 6rem;
        }
    }

    .author-card__info {
        flex: 1;
        min-width: 0;
    }

    .author-card__name {
        font-family: var(--font-serif);
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--color-text);
        margin: 0 0 0.125rem;
        line-height: 1.3;
    }

    .author-card__title {
        font-size: 0.8125rem;
        color: var(--color-accent);
        margin: 0 0 0.75rem;
        font-weight: 500;
    }

    .author-card__bio {
        font-size: 0.9375rem;
        line-height: 1.65;
        color: var(--color-text-muted);
    }
    .author-card__bio p {
        margin: 0 0 0.5em;
    }
    .author-card__bio p:last-child {
        margin-bottom: 0;
    }

    .author-card__socials {
        margin-top: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .author-card__socials-label {
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--color-text-faint);
    }

    .author-card__socials-row {
        display: flex;
        gap: 0.5rem;
    }

    .author-social-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: var(--radius-md);
        background: var(--color-surface-2);
        border: 1px solid var(--color-border);
        transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
    }
    .author-social-link:hover {
        background: var(--color-accent);
        border-color: var(--color-accent);
        transform: translateY(-2px);
    }
    .author-social-icon {
        width: 0.875rem;
        height: 0.875rem;
        fill: var(--color-text-muted);
        transition: fill 0.2s ease;
        pointer-events: none;
    }
    .author-social-link:hover .author-social-icon {
        fill: var(--color-text-inv);
    }

    @media (max-width: 639px) {
        .author-card__content {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .author-card__socials {
            justify-content: center;
        }
    }

    /* Related posts */
    .related-posts {
        background: var(--color-surface);
        border-top: 1px solid var(--color-border);
        padding: 3rem 1.5rem;
    }
    @media (min-width: 768px) {
        .related-posts {
            padding: 4rem 2rem;
        }
    }

    .related-posts__heading {
        font-family: var(--font-serif);
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--color-text);
        text-align: center;
        margin-bottom: 2rem;
    }

    .related-posts__grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        max-width: 70rem;
        margin: 0 auto;
    }
    @media (min-width: 768px) {
        .related-posts__grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .related-post {
        background: var(--color-bg);
        border: 1px solid var(--color-border-soft);
        border-radius: var(--radius-md);
        padding: 1.5rem;
        text-decoration: none;
        transition: transform 0.2s ease, border-color 0.2s ease;
        display: flex;
        flex-direction: column;
    }
    .related-post:hover {
        transform: translateY(-4px);
        border-color: var(--color-accent);
    }

    .related-post__date {
        font-size: 0.75rem;
        color: var(--color-accent);
        margin-bottom: 0.5rem;
    }

    .related-post__title {
        font-family: var(--font-serif);
        font-size: 1rem;
        font-weight: 600;
        line-height: 1.4;
        color: var(--color-text);
        margin: 0 0 0.5rem;
    }

    .related-post__author {
        font-size: 0.8125rem;
        color: var(--color-text-muted);
        margin-top: auto;
    }
</style>
@endsection

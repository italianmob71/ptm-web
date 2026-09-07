@extends('layouts.app')

@section('content')
<style>














    /* ===== Feature Cards Grid ===== */
    .feature-cards {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 1.5rem;
        padding: 3rem 1.5rem;
    }
    @media (min-width: 768px) {
        .feature-cards {
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            padding: 4rem 2rem;
            max-width: 80rem;
            margin: 0 auto;
        }
    }

    .feature-card {
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        display: flex;
        flex-direction: column;
    }
    .feature-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--color-accent);
    }

    .feature-card__image {
        width: 100%;
        aspect-ratio: 1 / 1;
        object-fit: cover;
        background: var(--color-surface-2);
    }

    .feature-card__content {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .feature-card__title {
        font-family: var(--font-serif);
        font-size: 1.125rem;
        font-weight: 600;
        line-height: 1.4;
        margin: 0 0 0.75rem;
        color: var(--color-text);
    }

    .feature-card__body {
        font-size: 0.875rem;
        line-height: 1.65;
        color: var(--color-text-muted);
        margin: 0;
        flex: 1;
    }

    /* ===== About Living Scroll Section ===== */
    .about-ptm {
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        margin: 3rem auto 0;
        max-width: 80rem;
        padding: 0;
        display: flex;
        flex-direction: column;
    }
    @media (min-width: 768px) {
        .about-ptm {
            flex-direction: row;
            margin: 4rem auto 0;
        }
    }

    .about-ptm__text {
        flex: 1;
        padding: 2.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    @media (min-width: 768px) {
        .about-ptm__text {
            padding: 3rem;
            min-width: 0;
        }
    }

    .about-ptm__title {
        font-family: var(--font-serif);
        font-size: 1.5rem;
        font-weight: 600;
        line-height: 1.3;
        margin: 0 0 1rem;
        color: var(--color-text);
    }
    @media (min-width: 768px) {
        .about-ptm__title {
            font-size: 1.75rem;
        }
    }

    .about-ptm__body {
        font-size: 1rem;
        line-height: 1.7;
        color: var(--color-text-muted);
        margin: 0 0 1.5rem;
    }

    .about-ptm__btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: var(--color-accent);
        color: var(--color-text-inv);
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: var(--radius-md);
        text-decoration: none;
        transition: background-color 0.2s ease, transform 0.1s ease;
        width: fit-content;
    }
    .about-ptm__btn:hover {
        background: var(--color-accent-hi);
        transform: translateY(-1px);
    }
    .about-ptm__btn:active {
        transform: translateY(0);
    }

    .about-ptm__video {
        flex: 1;
        min-height: 280px;
        position: relative;
        background: #000;
    }
    @media (min-width: 768px) {
        .about-ptm__video {
            min-height: 360px;
            max-width: 50%;
        }
    }
    html[data-theme="light"] .about-ptm__video {
        background: #fff;
    }

    .about-ptm__video video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* ===== Blog Section ===== */
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
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            padding: 2.5rem 3rem 3rem;
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
        margin: 0 0 1.5rem;
        flex: 1;
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

































    /* ===== Donate Section ===== */
    .donate-section {
        padding: 0 1.5rem 4rem;
    }
    @media (min-width: 768px) {
        .donate-section {
            padding: 0 2rem 5rem;
        }
    }

    .donate-card {
        display: flex;
        flex-direction: column;
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        max-width: 80rem;
        margin: 4rem auto 0;
    }
    @media (min-width: 768px) {
        .donate-card {
            flex-direction: row;
            margin: 4rem auto 0;
        }
    }

    .donate-card__part {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    @media (min-width: 768px) {
        .donate-card__part {
            flex: 1 1 0;
        }
    }

    /* Part 1: Text */
    .donate-card__text {
        background: var(--color-bg);
        padding: 2.5rem;
        justify-content: center;
    }
    @media (min-width: 768px) {
        .donate-card__text {
            padding: 3rem;
            min-width: 280px;
        }
    }

    .donate-card__text-inner {
        width: 100%;
        max-width: 360px;
        margin: 0 auto;
        text-align: center;
    }
    @media (min-width: 768px) {
        .donate-card__text-inner {
            text-align: left;
            margin: 0;
        }
    }

    .donate-card__title {
        font-family: var(--font-serif);
        font-size: 1.75rem;
        font-weight: 600;
        line-height: 1.3;
        margin: 0 0 1rem;
        color: var(--color-text);
    }
    @media (min-width: 768px) {
        .donate-card__title {
            font-size: 2rem;
        }
    }

    .donate-card__body {
        font-size: 1rem;
        line-height: 1.7;
        color: var(--color-text-muted);
        margin: 0 0 1.5rem;
    }

    .donate-card__btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: var(--color-accent);
        color: var(--color-text-inv);
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: var(--radius-md);
        text-decoration: none;
        transition: background-color 0.2s ease, transform 0.1s ease;
        width: fit-content;
    }
    .donate-card__btn:hover {
        background: var(--color-accent-hi);
        transform: translateY(-1px);
    }

    /* Part 2: Image - sets the height constraint */
    .donate-card__image {
        position: relative;
        background: var(--color-surface-2);
        flex-shrink: 0;
    }
    @media (min-width: 768px) {
        .donate-card__image {
            max-width: 33.33%;
        }
    }

    .donate-card__img {
        width: 100%;
        height: 100%;
        min-height: 280px;
        object-fit: cover;
        display: block;
    }
    @media (min-width: 768px) {
        .donate-card__img {
            min-height: 360px;
        }
    }

    /* Part 3: Logo */
    .donate-card__logo {
        background: var(--color-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }
    @media (min-width: 768px) {
        .donate-card__logo {
            min-width: 200px;
            max-width: 200px;
            padding: 3rem;
        }
    }

    .donate-card__logo-inner {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .donate-logo {
        display: none;
        height: auto;
        width: auto;
        max-height: 80px;
        max-width: 160px;
        object-fit: contain;
    }
    @media (max-width: 767px) {
        .donate-logo {
            max-height: 60px;
            max-width: 140px;
        }
    }

    html[data-theme=dark] .donate-logo-dark {
        display: block;
    }
    html[data-theme=light] .donate-logo-light {
        display: block;
    }
</style>

<!-- About Living Scroll Section -->
<section class="about-ptm" aria-labelledby="about-heading">
    <div class="about-ptm__text">
        <h2 id="about-heading" class="about-ptm__title">About Living Scroll</h2>
        <p class="about-ptm__body">
            Living Scroll is a place where ancient Scripture comes alive. Centered on the Cochin Hebrew New Testament, this website shares studies, teachings, and discoveries focused on healings, miracles, and the powerful witness of the New Testament in Hebrew. Through these studies, Living Scroll seeks to validate the Cochin Hebrew New Testament and reveal how its message continues to speak, heal, and inspire today.
        </p>
        <a href="{{ route('about') }}" class="about-ptm__btn">Learn More <span aria-hidden="true">→</span></a>
    </div>

    <div class="about-ptm__video" aria-label="Living Scroll introduction video">
        <video controls playsinline poster="{{ asset('images/site/studies-500x500-1.jpg') }}">
            <source src="{{ asset('videos/ptm-home.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
</section>



<!-- Truths Revealed Blog Section -->
<section class="blog-section" aria-labelledby="blog-heading">
    <div class="blog-card">
        <!-- Section Title Bar -->
        <header class="blog-card__header">
            <div class="blog-card__title-lines">
                <span class="blog-card__line1">Living Scroll &ndash; Presents</span>
                <span class="blog-card__line2">Truths Revealed Blog</span>
            </div>
        </header>

        <!-- Blog Posts Grid -->
        <div class="blog-grid">
            @forelse ($latestPosts as $post)
                <article class="blog-post">
                    @if ($post->featured_image)
                        <img src="{{ asset('images/site/' . $post->featured_image) }}"
                             alt="{{ $post->title }}"
                             class="blog-post__image"
                             loading="lazy" />
                    @endif
                    <div class="blog-post__content">
                        <time style="display: block; font-size: 0.75rem; color: var(--color-accent); margin-bottom: 0.5rem;">
                            {{ $post->published_at?->format('F j, Y') ?? $post->created_at->format('F j, Y') }}
                        </time>
                        <h3 class="blog-post__title">
                            <a href="{{ route('blog.show', $post->slug) }}" style="color: inherit; text-decoration: none;">{{ $post->title }}</a>
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
            @empty
                <article class="blog-post">
                    <div class="blog-post__content">
                        <h3 class="blog-post__title">No posts yet</h3>
                        <p class="blog-post__excerpt">Check back soon for new articles.</p>
                    </div>
                </article>
            @endforelse
        </div>
    </div>
</section>



<!-- Donate Section -->
<section class="donate-section" aria-labelledby="donate-heading">
    <div class="donate-card">
        <!-- Part 1: Text / CTA -->
        <div class="donate-card__part donate-card__text">
            <div class="donate-card__text-inner">
                <h2 id="donate-heading" class="donate-card__title">Support Our Cause</h2>
                <p class="donate-card__body">
                    Join us in uncovering the truth of Yehovah&rsquo;s Word through Yeshua. Your support fuels research, translation, and the revelation of ancient Hebrew manuscripts that illuminate the path of faith.
                </p>
                <a href="https://paypal.biz/livingscroll" class="donate-card__btn" target="_blank" rel="noopener noreferrer">Donate Now <span aria-hidden="true">&rarr;</span></a>
            </div>
        </div>

        <!-- Part 2: Image -->
        <div class="donate-card__part donate-card__image">
            <img src="{{ asset('images/site/revelation-500x500-1.jpg') }}"
                 alt=""
                 class="donate-card__img"
                 loading="lazy" />
        </div>

        <!-- Part 3: Logo -->
        <div class="donate-card__part donate-card__logo">
            <div class="donate-card__logo-inner">
                <img src="{{ asset('images/logos/png/white-on-black-optimized-300dpi.png') }}"
                     alt="Living Scroll"
                     class="donate-logo donate-logo-dark" />
                <img src="{{ asset('images/logos/png/black-on-white-optimized-300dpi.png') }}"
                     alt="Living Scroll"
                     class="donate-logo donate-logo-light" />
            </div>
        </div>
    </div>
</section>

@endsection
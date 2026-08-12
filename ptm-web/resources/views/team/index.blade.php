@extends('layouts.app')

@section('content')
<!-- Team Page Header -->
<header class="py-12 md:py-16" style="background-color: var(--color-surface); border-bottom: 1px solid var(--color-border);">
    <div class="mx-auto max-w-7xl px-4 text-center">
        <h1 class="text-3xl md:text-4xl font-serif font-semibold" style="color: var(--color-text); margin-bottom: 0.5rem;">Our Team</h1>
        <p class="text-lg max-w-2xl mx-auto" style="color: var(--color-text-muted);">
            Meet the researchers, translators, and engineers behind Project Truth Ministries
        </p>
    </div>
</header>

<main>
    <div class="mx-auto max-w-7xl px-4 py-12">
        <!-- Team Grid -->
        <div class="team-grid">
            @foreach ($teamMembers as $member)
                <article class="team-card">
                    <div class="team-card__image-wrapper">
                        <img src="{{ asset('images/authors/' . $member->image) }}"
                             alt="{{ $member->full_name }}"
                             class="team-card__image"
                             loading="lazy" />
                    </div>
                    <div class="team-card__content">
                        <h3 class="team-card__name">{{ $member->full_name }}</h3>
                        @if ($member->title)
                            <p class="team-card__title">{{ $member->title }}</p>
                        @endif
                        <div class="team-card__bio prose-scholarly">
                            {!! $member->bio !!}
                        </div>
                        @if ($member->social_profiles)
                            <div class="team-card__socials">
                                <p class="team-card__socials-label">Socials</p>
                                <div class="team-card__socials-row">
                                    @foreach ($member->social_profiles as $social)
                                        <a href="{{ $social['url'] }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="team-social-link"
                                           title="{{ ucfirst($social['platform']) }}"
                                           aria-label="{{ $member->full_name }} on {{ ucfirst($social['platform']) }}">
                                            @if ($social['platform'] === 'x')
                                                <svg class="team-social-icon" viewBox="0 0 1200 1227" aria-hidden="true">
                                                    <path fill="currentColor" d="M714.163 519.284L1160.89 0H1055.03L667.137 450.887L357.328 0H0L468.492 681.821L0 1226.37H105.866L515.491 750.218L842.672 1226.37H1200L714.137 519.284H714.163ZM569.165 687.828L521.697 619.934L144.011 79.6944H306.615L611.412 515.685L658.88 583.579L1055.08 1150.3H892.476L569.165 687.854V687.828Z"/>
                                                </svg>
                                            @else
                                                <svg class="team-social-icon" aria-hidden="true">
                                                    <use xlink:href="#{{ $social['icon'] }}"></use>
                                                </svg>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</main>

<style>
    /* ===== Team Page Styles ===== */
    .team-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
        max-width: 80rem;
        margin: 0 auto;
    }
    @media (min-width: 768px) {
        .team-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (min-width: 1024px) {
        .team-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .team-card {
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        display: flex;
        flex-direction: column;
    }
    .team-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--color-accent);
    }

    .team-card__image-wrapper {
        width: 100%;
        aspect-ratio: 3 / 4;
        background: var(--color-surface-2);
        overflow: hidden;
    }
    .team-card__image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .team-card:hover .team-card__image {
        transform: scale(1.03);
    }

    .team-card__content {
        padding: 1.75rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .team-card__name {
        font-family: var(--font-serif);
        font-size: 1.25rem;
        font-weight: 600;
        line-height: 1.3;
        margin: 0 0 0.25rem;
        color: var(--color-text);
    }

    .team-card__title {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--color-accent);
        margin: 0 0 1rem;
        text-transform: none;
        letter-spacing: 0;
    }

    .team-card__bio {
        font-size: 0.9375rem;
        line-height: 1.7;
        color: var(--color-text-muted);
        flex: 1;
    }
    .team-card__bio p {
        margin: 0 0 0.75em;
    }
    .team-card__bio p:last-child {
        margin-bottom: 0;
    }

    /* Social icons */
    .team-card__socials {
        margin-top: 1.25rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--color-border);
    }
    .team-card__socials-label {
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--color-text-faint);
        margin: 0 0 0.625rem;
    }
    .team-card__socials-row {
        display: flex;
        gap: 0.625rem;
    }
    .team-social-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: var(--radius-md);
        background: var(--color-surface-2);
        border: 1px solid var(--color-border);
        transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
    }
    .team-social-link:hover {
        background: var(--color-accent);
        border-color: var(--color-accent);
        transform: translateY(-2px);
    }
    .team-social-icon {
        width: 1.125rem;
        height: 1.125rem;
        fill: var(--color-text-muted);
        transition: fill 0.2s ease;
        pointer-events: none;
        display: block;
        overflow: hidden;
    }
    .team-social-link:hover .team-social-icon {
        fill: var(--color-text-inv);
    }

    /* Responsive tweaks */
    @media (max-width: 767px) {
        .team-card__content {
            padding: 1.25rem;
        }
        .team-card__name {
            font-size: 1.125rem;
        }
    }
</style>
@endsection

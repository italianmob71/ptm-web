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
                        <div class="team-card__bio prose-scholarly">
                            {!! $member->bio !!}
                        </div>
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
        margin: 0 0 1rem;
        color: var(--color-text);
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
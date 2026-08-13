@extends('layouts.app')

@section('content')
<section class="tn-section" aria-labelledby="tn-heading">
    <div class="tn-card">
        <!-- Header -->
        <header class="tn-card__header">
            <div class="tn-card__title-lines">
                <span class="tn-card__line1">Project Truth Ministries &ndash; Presents</span>
                <span class="tn-card__line2">Bryan's Travel Notes</span>
            </div>
            <form method="GET" action="{{ route('travel-notes.index') }}" class="tn-search-form">
                <input type="search" name="q" value="{{ $search ?? '' }}"
                       placeholder="Search travel notes..."
                       class="tn-search-input" aria-label="Search travel notes">
                <button type="submit" class="tn-search-btn">
                    <svg class="btn-icon" aria-hidden="true"><use xlink:href="#icon-search"></use></svg>
                    Search
                </button>
            </form>
        </header>

        @if (request('q'))
            <div class="tn-search-results-bar">
                <p>Search results for &ldquo;<strong>{{ $search }}</strong>&rdquo; — {{ $notes->total() }} note(s) found.</p>
                <a href="{{ route('travel-notes.index') }}" class="tn-search-clear">Clear search &times;</a>
            </div>
        @endif

        @if ($notes->isNotEmpty())
            <div class="tn-grid">
                @foreach ($notes as $note)
                    <article class="tn-card-item">
                        @if ($note->teaserImage)
                            <div class="tn-card-item__thumb">
                                <img src="{{ $note->teaserImage->url }}" alt="{{ $note->title }}" loading="lazy">
                            </div>
                        @else
                            <div class="tn-card-item__thumb tn-card-item__thumb--empty">
                                <span style="font-size: 2rem; opacity: 0.3;">&#9998;</span>
                            </div>
                        @endif
                        <div class="tn-card-item__content">
                            @if ($note->biblical_reference)
                                <span class="tn-card-item__ref">{{ $note->biblical_reference }}</span>
                            @endif
                            <h3 class="tn-card-item__title">
                                <a href="{{ route('travel-notes.show', $note->slug) }}">{{ $note->title }}</a>
                            </h3>
                            @if ($note->location)
                                <p class="tn-card-item__location">{{ $note->location }}</p>
                            @endif
                            <a href="{{ route('travel-notes.show', $note->slug) }}" class="tn-card-item__btn">
                                Read More <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($notes->hasPages())
                <div class="tn-pagination">{{ $notes->links() }}</div>
            @endif
        @else
            <div class="tn-empty">
                @if (request('q'))
                    <p>No travel notes found for &ldquo;{{ $search }}&rdquo;.</p>
                    <a href="{{ route('travel-notes.index') }}" class="tn-search-clear">Clear search &times;</a>
                @else
                    <p>No travel notes have been published yet.</p>
                @endif
            </div>
        @endif
    </div>
</section>

<style>
    .tn-section { padding: 0 1.5rem 4rem; }
    @media (min-width: 768px) { .tn-section { padding: 0 2rem 5rem; } }

    .tn-card {
        background: var(--color-surface); border: 1px solid var(--color-border);
        border-radius: var(--radius-lg); overflow: hidden;
        max-width: 80rem; margin: 4rem auto 0;
    }

    .tn-card__header {
        background: var(--color-bg); border-bottom: 1px solid var(--color-border);
        padding: 2rem 2.5rem;
    }
    @media (min-width: 768px) { .tn-card__header { padding: 2.5rem 3rem; } }

    .tn-card__title-lines { display: flex; flex-direction: column; gap: 0.25rem; }
    .tn-card__line1 {
        font-family: var(--font-sans); font-size: 0.875rem; font-weight: 500;
        letter-spacing: 0.1em; text-transform: uppercase; color: var(--color-accent);
    }
    .tn-card__line2 {
        font-family: var(--font-serif); font-size: 2rem; font-weight: 600;
        line-height: 1.2; color: var(--color-text);
    }
    @media (min-width: 768px) { .tn-card__line2 { font-size: 2.5rem; } }

    .tn-grid {
        display: grid; grid-template-columns: 1fr; gap: 1.5rem; padding: 2rem;
    }
    @media (min-width: 640px) { .tn-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width: 1024px) { .tn-grid { grid-template-columns: repeat(3, 1fr); padding: 2.5rem 3rem 3rem; } }

    .tn-card-item {
        background: var(--color-bg); border: 1px solid var(--color-border-soft);
        border-radius: var(--radius-md); overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        display: flex; flex-direction: column;
    }
    .tn-card-item:hover {
        transform: translateY(-4px); box-shadow: var(--shadow-lg);
        border-color: var(--color-accent);
    }

    .tn-card-item__thumb { aspect-ratio: 16/10; overflow: hidden; background: var(--color-surface-2); }
    .tn-card-item__thumb img { width: 100%; height: 100%; object-fit: cover; }
    .tn-card-item__thumb--empty { display: flex; align-items: center; justify-content: center; }

    .tn-card-item__content { padding: 1.5rem; display: flex; flex-direction: column; flex: 1; }
    .tn-card-item__ref {
        font-size: 0.75rem; font-weight: 600; color: var(--color-accent);
        margin-bottom: 0.5rem; display: block;
    }
    .tn-card-item__title {
        font-family: var(--font-serif); font-size: 1.125rem; font-weight: 600;
        line-height: 1.4; margin: 0 0 0.5rem; color: var(--color-text);
    }
    .tn-card-item__title a { color: inherit; text-decoration: none; }
    .tn-card-item__location { font-size: 0.8125rem; color: var(--color-text-muted); margin: 0 0 1rem; }

    .tn-card-item__btn {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.625rem 1.25rem; background: transparent;
        border: 2px solid var(--color-accent); color: var(--color-accent);
        font-size: 0.8125rem; font-weight: 600; border-radius: var(--radius-md);
        text-decoration: none; width: fit-content; margin-top: auto;
        transition: background-color 0.2s ease, color 0.2s ease, transform 0.1s ease;
    }
    .tn-card-item__btn:hover { background: var(--color-accent); color: var(--color-text-inv); transform: translateX(4px); }

    .tn-pagination { padding: 0 2rem 2.5rem; display: flex; justify-content: center; }
    @media (min-width: 768px) { .tn-pagination { padding: 0 3rem 3rem; } }
    .tn-pagination nav { display: flex; gap: 0.5rem; }
    .tn-pagination a, .tn-pagination span {
        padding: 0.5rem 0.75rem; border-radius: var(--radius-sm);
        font-size: 0.875rem; text-decoration: none;
        border: 1px solid var(--color-border); color: var(--color-text);
    }
    .tn-pagination .current { background: var(--color-accent); color: var(--color-text-inv); border-color: var(--color-accent); }

    .tn-search-form { display: flex; align-items: center; gap: 0.5rem; margin-top: 1.25rem; max-width: 28rem; }
    .tn-search-input {
        flex: 1; height: 2.25rem; padding: 0 0.875rem;
        border: 1px solid var(--color-border); border-radius: var(--radius-sm);
        background-color: var(--color-bg); color: var(--color-text);
        font-size: 0.8125rem; outline: none; transition: border-color 0.2s ease;
    }
    .tn-search-input:focus { border-color: var(--color-accent); }
    .tn-search-input::placeholder { color: var(--color-text-faint); }
    .tn-search-btn {
        display: inline-flex; align-items: center; gap: 0.375rem;
        height: 2.25rem; padding: 0 1rem; background: transparent;
        border: 1px solid var(--color-accent); color: var(--color-accent);
        font-size: 0.75rem; font-weight: 600; border-radius: var(--radius-sm);
        cursor: pointer; white-space: nowrap;
        transition: background-color 0.2s ease, color 0.2s ease;
    }
    .tn-search-btn:hover { background: var(--color-accent); color: var(--color-text-inv); }

    .tn-search-results-bar {
        display: flex; align-items: center; justify-content: space-between;
        gap: 1rem; padding: 1rem 2rem;
        background: var(--color-surface-2); border-bottom: 1px solid var(--color-border);
        font-size: 0.875rem; color: var(--color-text-muted);
    }
    @media (min-width: 768px) { .tn-search-results-bar { padding: 1rem 3rem; } }
    .tn-search-results-bar strong { color: var(--color-text); }
    .tn-search-clear { font-size: 0.8125rem; color: var(--color-accent); text-decoration: none; white-space: nowrap; }
    .tn-search-clear:hover { opacity: 0.7; }

    .tn-empty { padding: 3rem 2rem; text-align: center; color: var(--color-text-muted); }
    .tn-empty p { font-size: 1.125rem; }
</style>
@endsection

@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/editor-content.css') }}">
<header class="py-12 md:py-16" style="background-color: var(--color-surface); border-bottom: 1px solid var(--color-border);">
    <div class="mx-auto max-w-7xl px-4 text-center">
        <h1 class="text-3xl md:text-4xl font-serif font-semibold" style="color: var(--color-text); margin-bottom: 0.5rem;">{{ $page->title }}</h1>
    </div>
</header>

<main class="research">
    <div class="mx-auto max-w-7xl px-4 py-12">
        <div class="prose-scholarly max-w-4xl mx-auto" style="color: var(--color-text); font-size: 1.0625rem; line-height: 1.8;">
            <section class="mb-12 editor-content ck-content" style="color: var(--color-text-muted);">
                {!! $page->content !!}
            </section>

            <section aria-label="Ongoing and upcoming Research Studies">
                @if ($studies->isEmpty())
                    <p class="no-studies">There are no ongoing or upcoming Research Studies. Please check again soon.</p>
                @else
                    <ul class="research-studies">
                        @foreach ($studies as $study)
                            <li class="research-study">
                                <img src="{{ asset($study->image_path) }}" alt="{{ $study->title }}" width="500" height="500" loading="lazy" class="research-study-image">
                                <div class="research-study-details">
                                    <p class="text-sm" style="color: var(--color-text-muted);">
                                        <time datetime="{{ $study->starts_at->toIso8601String() }}">{{ $study->starts_at->format('F j, Y, g:i a') }}</time>
                                        &ndash;
                                        <time datetime="{{ $study->ends_at->toIso8601String() }}">{{ $study->ends_at->format('F j, Y, g:i a') }}</time>
                                    </p>
                                    <h2 class="font-serif text-2xl font-semibold mb-3">{{ $study->title }}</h2>
                                    <div class="editor-content ck-content mb-4">
                                        {!! $study->description !!}
                                        <p>
                                            @if ($study->applicationsClosed())
                                                <a class="research-study-apply disabled" role="link" aria-disabled="true">Please complete the pre-study form.</a>
                                            @else
                                                <a href="{{ $study->application_url }}" target="_blank" rel="noopener noreferrer">Please complete the pre-study form.</a>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
    </div>
</main>
<style>
.research-studies { list-style: none; padding: 0; margin: 0; }
.research-study { display: flex; gap: 1.5rem; padding: 2rem 0; border-top: 1px solid var(--color-border); align-items: flex-start; }
.research-study-image { width: 275px; max-width: 100%; height: auto; aspect-ratio: 1; object-fit: cover; flex-shrink: 0; border-radius: 0.5rem; }
.research-study-details { min-width: 0; overflow-wrap: anywhere; }
.research-study-details > p, .research-study-details > h2 { padding-inline: .5rem; }
.research-study-details img { max-width: 100%; height: auto; }
.research-study-details .editor-content { overflow-x: auto; padding-inline: .5rem; }
.research-study-apply.disabled { opacity: 0.5; cursor: not-allowed; }
time { font-weight: bold; font-size: 16px; }
@media (max-width: 767.98px) {
    .research-study { flex-direction: column; }
    .research-study-image { width: 100%; }
}
.research a {
  text-decoration: none !important;
}
.research a:hover {
  text-decoration: underline !important;
}
.no-studies { margin-left: 1.5rem; }
</style>
@endsection

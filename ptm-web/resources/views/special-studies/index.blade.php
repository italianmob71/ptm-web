@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/editor-content.css') }}">
<header class="py-12 md:py-16" style="background-color: var(--color-surface); border-bottom: 1px solid var(--color-border);">
    <div class="mx-auto max-w-7xl px-4 text-center">
        <h1 class="text-3xl md:text-4xl font-serif font-semibold" style="color: var(--color-text); margin-bottom: 0.5rem;">{{ $page->title }}</h1>
    </div>
</header>

<main>
    <div class="mx-auto max-w-7xl px-4 py-12">
        <div class="prose-scholarly max-w-4xl mx-auto" style="color: var(--color-text); font-size: 1.0625rem; line-height: 1.8;">

            <section class="mb-12 ck-content">
                {!! $page->description !!}
            </section>
            <section aria-labelledby="special-study-downloads">
                <h2 id="special-study-downloads" class="font-serif text-2xl font-semibold mb-4">Special Studies for Download</h2>
                <ul class="pdf-list" style="list-style:none; padding:0;">
                    @forelse ($studies as $study)
                        <li style="padding:1rem 0;">
                            <a href="{{ $study->pdf->url }}" title="{{ trim($study->pdf->description ?? '') ?: 'No Description' }}" style="display:flex; align-items:center; gap:1rem; overflow-wrap:anywhere;">
                                <x-pdf-icon />
                                <span>{{ $study->pdf->title ?: $study->pdf->filename }}</span>
                            </a>
                        </li>
                    @empty
                        <li>No special studies are available for download yet.</li>
                    @endforelse
                </ul>
            </section>

        </div>
    </div>
</main>
<style>
  .pdf-list { border: 2px solid var(--color-surface); }
  .pdf-list li > a {
    padding: 0 10px;
  }
  .pdf-list li:nth-child(even) {
    background-color: var(--color-surface);
  }
</style>
@endsection

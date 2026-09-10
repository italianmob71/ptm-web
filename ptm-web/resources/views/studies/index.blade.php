@extends('layouts.app')

@section('content')
<!-- Studies Page Header -->
<header class="py-12 md:py-16" style="background-color: var(--color-surface); border-bottom: 1px solid var(--color-border);">
    <div class="mx-auto max-w-7xl px-4 text-center">
        <h1 class="text-3xl md:text-4xl font-serif font-semibold" style="color: var(--color-text); margin-bottom: 0.5rem;">Living Scroll Studies</h1>
    </div>
</header>

<main>
    <div class="mx-auto max-w-7xl px-4 py-12">
        <div class="prose-scholarly max-w-4xl mx-auto" style="color: var(--color-text); font-size: 1.0625rem; line-height: 1.8;">
            <section class="mb-12">
                <ul class="list-disc pl-6" style="color: var(--color-text-muted);">
                    <li id="cochin">
                        <a href="#cochin">Cochin Hebrew New Testament</a>
                        @if ($cochinBooks->isNotEmpty())
                            <ul class="list-disc pl-6">
                                @foreach ($cochinBooks as $book)
                                    <li><a href="{{ route('cochin.show', $book->slug) }}">{{ $book->title }}</a></li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                    <li><a href="{{ route('renewed-covenant') }}">Renewed Covenant</a></li>
                    <li><a href="{{ route('special-studies') }}">Special Studies</a></li>
                    <li><a href="{{ route('get-involved') }}">Get Involved in Research</a></li>
                </ul>
            </section>
        </div>
    </div>
</main>
@endsection

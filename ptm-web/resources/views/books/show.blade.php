@extends('layouts.app')

@section('content')
<style>.deets a:hover { text-decoration: none !important }</style>
<!-- Book Detail Page -->
<main>
    <div class="mx-auto max-w-5xl px-4 py-12">
        {{-- Breadcrumb --}}
        <nav class="mb-6 text-sm" style="color: var(--color-text-muted);" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li><a href="{{ route('home') }}" class="hover:underline">Home</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('books.index') }}" class="hover:underline">Book Recommendations</a></li>
                <li aria-hidden="true">/</li>
                <li aria-current="page" style="color: var(--color-text);">{{ $book->title }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Cover & Buy Links --}}
            <div class="lg:col-span-1">
                <div class="relative mb-6">
                    @if($book->image_front)
                        <img src="{{ asset($book->image_front) }}"
                             alt="{{ $book->title }}"
                             class="w-full aspect-[2/3] object-cover rounded-lg shadow-lg"
                             style="border: 1px solid var(--color-border);">
                    @else
                        <div class="w-full aspect-[2/3] rounded-lg flex items-center justify-center"
                             style="background-color: var(--color-surface-2); border: 1px solid var(--color-border);">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--color-text-muted);">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                    @if($book->published)
                        <span class="absolute top-4 left-4 px-3 py-1 text-xs font-semibold uppercase rounded-full"
                              style="background-color: var(--color-success); color: var(--color-text-inv);">Published</span>
                    @else
                        <span class="absolute top-4 left-4 px-3 py-1 text-xs font-semibold uppercase rounded-full"
                              style="background-color: var(--color-text-muted); color: var(--color-text-inv);">Draft</span>
                    @endif
                </div>

                {{-- Buy Links --}}
                <div class="space-y-3">
                    @if($book->amazon_link)
                        <a href="{{ $book->amazon_link }}" target="_blank" rel="noopener noreferrer"
                           class="block w-full py-3 px-4 text-center font-medium rounded-lg transition"
                           style="background-color: #ff9900; color: #111;">
                            <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M19.26 12.49c0-1.28-.57-2.4-1.5-3.21A7.5 7.5 0 0012 4.5c-2.42 0-4.62 1.09-6.12 2.82A4.5 4.5 0 004.26 12.5c0 .15 0 .3.01.44A4.3 4.3 0 004 12.5a4.5 4.5 0 001.26 3.55c0 1.28.57 2.4 1.5 3.21A7.5 7.5 0 0012 19.5c2.42 0 4.62-1.09 6.12-2.82A4.5 4.5 0 0019.74 11.5c0-.15 0-.3.01-.44 0 0 0 0 0 0z"/><path d="M12 8.5c-.5 0-.9.4-.9.9 0 .49.4.9.9.9.5 0 .9-.4.9-.9 0-.49-.4-.9-.9-.9z"/></svg>
                            Buy on Amazon
                        </a>
                    @endif
                    @if($book->lulu_link)
                        <a href="{{ $book->lulu_link }}" target="_blank" rel="noopener noreferrer"
                           class="block w-full py-3 px-4 text-center font-medium rounded-lg border transition"
                           style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 19 7.5 19s3.332.477 4.5 1.253v-13C13.168 18.477 14.754 19 16.5 19s3.332.477 4.5 1.253V6.253C19.832 5.477 18.246 5 16.5 5S13.168 5.477 12 6.253z"/></svg>
                            Buy on Lulu
                        </a>
                    @endif
                    @if($book->isbn_13)
                        <div class="text-center text-xs p-2" style="background-color: var(--color-surface-2); border: 1px solid var(--color-border); border-radius: var(--radius-md); color: var(--color-text-muted);">
                            ISBN-13: {{ $book->isbn_13 }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Details & Description --}}
            <div class="lg:col-span-2">
                <header class="mb-6">
                    <h1 class="font-serif text-3xl md:text-4xl font-bold" style="color: var(--color-text);">{{ $book->title }}</h1>
                    @if($book->subtitle)
                        <h2 class="font-serif text-xl mt-2 font-normal italic" style="color: var(--color-text-muted);">{{ $book->subtitle }}</h2>
                    @endif

                    <div class="flex flex-wrap items-center gap-4 mt-4" style="color: var(--color-text-muted);">
                        <span class="font-medium">
                            @php($a = $book->author)
                            @if($a)
                                By {{ $a->full_name ?? trim(($a->first_name ?? '') . ' ' . ($a->last_name ?? '')) ?: 'Unknown' }}
                            @else
                                By Unknown
                            @endif
                        </span>
                        @if($book->published_at)
                            <time datetime="{{ $book->published_at->format('Y-m-d') }}">{{ $book->published_at->format('F Y') }}</time>
                        @endif
                        @if($book->page_count)
                            <span>{{ $book->page_count }} pages</span>
                        @endif
                        @if($book->edition)
                            <span>{{ $book->edition }}</span>
                        @endif
                        @if($book->language)
                            <span>{{ $book->language }}</span>
                        @endif
                    </div>
                </header>

                {{-- Description --}}
                <div class="prose" style="color: var(--color-text);">
                    @if($book->body)
                        {!! $book->body !!}
                    @else
                        <p class="text-lg" style="color: var(--color-text-muted);">No description available.</p>
                    @endif
                </div>

                {{-- Specifications --}}
                @if($book->isbn_13 || $book->isbn_10 || $book->price_usd)
                <div class="mt-8 pt-6 border-t" style="border-color: var(--color-border);">
                    <h3 class="font-semibold mb-4" style="color: var(--color-text);">Details</h3>
                    <dl class="grid grid-cols-2 gap-3 text-sm" style="color: var(--color-text-muted);">
                        @if($book->isbn_13)
                            <dt>ISBN-13</dt>
                            <dd class="font-mono">{{ $book->isbn_13 }}</dd>
                        @endif
                        @if($book->isbn_10)
                            <dt>ISBN-10</dt>
                            <dd class="font-mono">{{ $book->isbn_10 }}</dd>
                        @endif
                        @if($book->price_usd)
                            <dt>Price</dt>
                            <dd>${{ number_format($book->price_usd, 2) }}</dd>
                        @endif
                        @if($book->page_count)
                            <dt>Pages</dt>
                            <dd>{{ $book->page_count }}</dd>
                        @endif
                        @if($book->edition)
                            <dt>Edition</dt>
                            <dd>{{ $book->edition }}</dd>
                        @endif
                        @if($book->language)
                            <dt>Language</dt>
                            <dd>{{ $book->language }}</dd>
                        @endif
                    </dl>
                </div>
                @endif
            </div>
        </div>

        {{-- Related Books --}}
        @if($otherBooks && $otherBooks->count() > 0)
        <section class="mt-16">
            <h2 class="font-serif text-2xl font-semibold mb-6" style="color: var(--color-text);">More Recommendations</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($otherBooks as $other)
                    <article class="group deets">
                        <a href="{{ route('books.show', $other->slug) }}">
                            <div class="aspect-[2/3] mb-3 overflow-hidden rounded-lg"
                                 style="background-color: var(--color-surface-2); border: 1px solid var(--color-border);">
                                @if($other->image_front)
                                    <img src="{{ asset($other->image_front) }}"
                                         alt="{{ $other->title }}"
                                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--color-text-muted);">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <h3 class="font-semibold text-sm" style="color: var(--color-text);">{{ $other->title }}</h3>
                            @if($other->subtitle)
                                <p class="text-xs italic" style="color: var(--color-text-muted);">{{ $other->subtitle }}</p>
                            @endif
                            <p class="text-xs mt-1" style="color: var(--color-accent);">
                                @php($a = $other->author)
                                @if($a)
                                    {{ $a->full_name ?? trim(($a->first_name ?? '') . ' ' . ($a->last_name ?? '')) }}
                                @endif
                            </p>
                        </a>
                    </article>
                @endforeach
            </div>
        </section>
        @endif
    </div>
</main>
@endsection
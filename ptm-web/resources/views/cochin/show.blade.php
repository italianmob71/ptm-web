@extends('layouts.app')

@section('content')
<div class="cochin-book" style="max-width: 56rem; margin: 0 auto; padding: 1rem;">

    {{-- Breadcrumb --}}
    <nav class="mb-4 text-sm" style="color: var(--color-text-faint);">
        <a href="{{ route('home') }}" style="color: var(--color-text-faint);">Home</a>
        <span class="mx-1">›</span>
        <span style="color: var(--color-text-faint);">Studies</span>
        <span class="mx-1">›</span>
        <span style="color: var(--color-text-faint);">Cochin Hebrew NT</span>
        <span class="mx-1">›</span>
        <span style="color: var(--color-text-muted);">{{ $book->title }}</span>
    </nav>

    {{-- ── HERO ───────────────────────────────────────── --}}
    <header class="cochin-hero" style="
        display: flex;
        gap: 1.5rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    ">
        @if ($book->coverImage)
        <div style="flex-shrink: 0;">
            <img src="{{ $book->coverImage->url }}" alt="{{ $book->title }}"
                 style="width: 180px; height: auto; border-radius: var(--radius-lg, 12px); box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
        </div>
        @endif

        <div style="flex: 1; min-width: 200px;">
            <h1 class="font-serif" style="font-size: 2rem; font-weight: 700; color: var(--color-text); margin: 0 0 0.5rem;">
                {{ $book->title }}
            </h1>
            @if ($book->manuscript)
            <p style="font-size: 0.875rem; color: var(--color-text-faint); margin: 0 0 0.75rem;">
                {{ $book->manuscript }}
            </p>
            @endif

            {{-- Status badge + progress --}}
            <div class="flex items-center gap-3 mb-3">
                @if ($book->is_wip)
                <span class="px-3 py-1 rounded-full text-xs font-medium" style="background: rgba(245,158,11,0.15); color: rgb(245,158,11);">
                    Work in Progress
                </span>
                @else
                <span class="px-3 py-1 rounded-full text-xs font-medium" style="background: rgba(34,197,94,0.15); color: rgb(34,197,94);">
                    Complete
                </span>
                @endif
                @if ($book->is_complete)
                <span style="font-size: 0.8125rem; color: var(--color-text-faint);">
                    {{ $book->total_chapters }} / {{ $book->total_chapters }} chapters
                </span>
                @else
                <span style="font-size: 0.8125rem; color: var(--color-text-faint);">
                    {{ $book->chapter_count }} / {{ $book->total_chapters }} chapters
                </span>
                @endif
            </div>

            {{-- Progress bar --}}
            <div style="height: 6px; border-radius: 3px; background: var(--color-surface-2, rgba(128,128,128,0.1)); overflow: hidden; margin-bottom: 1rem; max-width: 300px;">
                <div style="height: 100%; width: {{ $book->is_complete ? 100 : $book->progress_percent }}%; background: var(--color-accent); border-radius: 3px; transition: width 0.4s ease;"></div>
            </div>
        </div>
    </header>

    {{-- ── DESCRIPTION ─────────────────────────────────── --}}
    @if ($book->description)
    <div class="cochin-description prose-scholarly" style="
        max-width: 50rem;
        color: var(--color-text-muted);
        font-size: 0.9375rem;
        line-height: 1.7;
        margin-bottom: 2rem;
    ">
        {!! $book->description !!}
    </div>
    @endif

    {{-- ── TABS ─────────────────────────────────────────── --}}
    <div class="cochin-tabs" x-data="{ tab: 'downloads' }" style="margin-bottom: 2rem;">
        {{-- Tab buttons --}}
        <div class="flex gap-1 border-b" style="border-color: var(--color-border);">
            <button @click="tab='downloads'"
                    class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors"
                    :class="tab === 'downloads' ? '' : 'border-transparent'"
                    :style="tab === 'downloads' ? 'border-color: var(--color-accent); color: var(--color-text);' : 'border-color: transparent; color: var(--color-text-faint);'">
                Downloads
            </button>
            @if ($book->discoveries)
            <button @click="tab='discoveries'"
                    class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors"
                    :class="tab === 'discoveries' ? '' : 'border-transparent'"
                    :style="tab === 'discoveries' ? 'border-color: var(--color-accent); color: var(--color-text);' : 'border-color: transparent; color: var(--color-text-faint);'">
                Discoveries
            </button>
            @endif
        </div>

        {{-- ── Downloads tab ── --}}
        <div x-show="tab === 'downloads'" class="pt-6">
            @if ($book->is_complete && $book->completePdf)
            {{-- Complete book download --}}
            <div class="mb-6 p-4 rounded-lg" style="border: 1px solid var(--color-border); background: var(--color-surface);">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 600; color: var(--color-text); margin: 0 0 4px;">Complete Book</h3>
                        <p style="font-size: 0.8125rem; color: var(--color-text-faint);">{{ $book->completePdf->filename }} ({{ $book->completePdf->file_size_human }})</p>
                    </div>
                    <a href="{{ $book->completePdf->url }}" download
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium"
                       style="background: var(--color-accent); color: var(--color-surface);">
                        <svg style="width: 1rem; height: 1rem;"><use href="#icon-pdf"></use></svg>
                        Download
                    </a>
                </div>
            </div>
            @endif

            {{-- Chapter downloads — ONLY for WIP books --}}
            @if ($book->is_wip)
            <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--color-text); margin: 0 0 1rem;">
                Individual Chapter Downloads
            </h3>

            @if ($book->publishedChapters->isEmpty())
            <p style="color: var(--color-text-faint); font-size: 0.875rem;">No chapters published yet.</p>
            @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($book->publishedChapters as $chapter)
                <div class="p-3 rounded-lg flex items-center gap-3" style="border: 1px solid var(--color-border); background: var(--color-surface);">
                    <span style="font-size: 1.25rem; font-weight: 700; color: var(--color-accent); min-width: 2rem; text-align: center;">{{ $chapter->chapter_number }}</span>
                    <div class="flex-1 min-w-0">
                        <p style="font-size: 0.8125rem; font-weight: 500; color: var(--color-text); margin: 0;">{{ $chapter->display_title }}</p>
                        @if ($chapter->pdf)
                        <p style="font-size: 0.6875rem; color: var(--color-text-faint); margin: 2px 0 0;">{{ $chapter->pdf->file_size_human }}</p>
                        @endif
                    </div>
                    @if ($chapter->pdf)
                    <a href="{{ $chapter->pdf->url }}" download
                       class="flex-shrink-0 p-2 rounded-lg" title="Download Chapter {{ $chapter->chapter_number }}"
                       style="background: var(--color-surface-2, rgba(128,128,128,0.06)); color: #E9352F;">
                        <svg style="width: 1.125rem; height: 1.125rem;"><use href="#icon-pdf"></use></svg>
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
            @endif {{-- end is_wip --}}
        </div>
        <div x-show="tab === 'discoveries'" x-cloak class="pt-6">
            <div class="prose-scholarly" style="max-width: 50rem; color: var(--color-text-muted); font-size: 0.9375rem; line-height: 1.7;">
                {!! $book->discoveries !!}
            </div>
        </div>
    </div>

    {{-- ── SEE ALSO ─────────────────────────────────────── --}}
    <x-see-also :source="$book" :limit="15" />
</div>

<style>
[x-cloak] { display: none !important; }
.cochin-description img { max-width: 100%; height: auto; border-radius: var(--radius-md, 8px); margin: 1rem 0; }
.cochin-description td img { max-width: 100% !important; height: auto !important; }
.cochin-description table { table-layout: auto; }
.cochin-description blockquote {
    border-left: 4px solid var(--color-accent);
    padding-left: 1rem;
    font-style: italic;
    color: var(--color-text-muted);
    background: var(--color-surface-2, rgba(128,128,128,0.04));
    border-radius: 0 var(--radius-md, 8px) var(--radius-md, 8px) 0;
    padding: 0.75rem 1rem;
}
</style>
@endsection

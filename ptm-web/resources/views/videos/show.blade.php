@extends('layouts.app')

@section('content')
<div class="mx-auto" style="max-width: 50rem;">

    {{-- Breadcrumb --}}
    <nav class="mb-4 text-sm" style="color: var(--color-text-faint);">
        <a href="{{ route('home') }}" style="color: var(--color-text-faint);">Home</a>
        <span class="mx-1">›</span>
        <span style="color: var(--color-text-muted);">{{ $video->title ?? $video->filename }}</span>
    </nav>

    {{-- Header --}}
    <header class="mb-6">
        <h1 class="font-serif text-3xl font-bold mb-2" style="color: var(--color-text);">
            {{ $video->title ?? $video->filename }}
        </h1>
        @if ($video->description)
        <p class="text-sm" style="color: var(--color-text-muted);">{!! $video->description !!}</p>
        @endif
        <div class="flex items-center gap-3 mt-3 text-xs" style="color: var(--color-text-faint);">
            @if ($video->category)
            <span class="px-2 py-0.5 rounded-full" style="background: var(--color-surface-2, rgba(128,128,128,0.08)); border: 1px solid var(--color-border);">{{ $video->category }}</span>
            @endif
            @if ($video->is_embedded)
            <span class="px-2 py-0.5 rounded-full uppercase font-medium" style="background: var(--color-surface-2, rgba(128,128,128,0.08)); border: 1px solid var(--color-border);">{{ $video->source_platform }}</span>
            @endif
        </div>
    </header>

    {{-- Video player --}}
    <div style="
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg, 12px);
        overflow: hidden;
        background: #000;
        aspect-ratio: 16/9;
    ">
        @if ($video->is_embedded && $video->embed_url)
            {{-- YouTube / Rumble embed --}}
            <iframe src="{{ $video->embed_url }}"
                    style="width: 100%; height: 100%; border: 0;"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    loading="lazy"></iframe>
        @elseif ($video->is_local && $video->path)
            {{-- Local MP4 --}}
            <video controls style="width: 100%; height: 100%; background: #000;">
                <source src="{{ asset('videos/' . $video->path) }}" type="video/mp4">
                Your browser doesn't support video playback.
            </video>
        @else
            {{-- Fallback: link to source --}}
            <div style="padding: 2rem; text-align: center;">
                <p style="color: var(--color-text-muted); margin-bottom: 1rem;">Video not available.</p>
                @if ($video->source_url)
                <a href="{{ $video->source_url }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium"
                   style="background: var(--color-accent); color: var(--color-surface);">
                    Watch on {{ ucfirst($video->source_platform) }}
                </a>
                @endif
            </div>
        @endif
    </div>

    {{-- See Also --}}
    <x-see-also :source="$video" :limit="15" />
</div>
@endsection

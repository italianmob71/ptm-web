@extends('layouts.app')

@section('content')
<div class="mx-auto" style="max-width: 50rem;">

    {{-- Breadcrumb --}}
    <nav class="mb-4 text-sm" style="color: var(--color-text-faint);">
        <a href="{{ route('home') }}" style="color: var(--color-text-faint);">Home</a>
        <span class="mx-1">›</span>
        <span style="color: var(--color-text-muted);">{{ $image->title ?? $image->filename }}</span>
    </nav>

    {{-- Header --}}
    <header class="mb-6">
        <h1 class="font-serif text-3xl font-bold mb-2" style="color: var(--color-text);">
            {{ $image->title ?? $image->filename }}
        </h1>
        @if ($image->alt_text)
        <p class="text-sm" style="color: var(--color-text-muted);">{{ $image->alt_text }}</p>
        @endif
        <div class="flex items-center gap-3 mt-3 text-xs" style="color: var(--color-text-faint);">
            @if ($image->category)
            <span class="px-2 py-0.5 rounded-full" style="background: var(--color-surface-2, rgba(128,128,128,0.08)); border: 1px solid var(--color-border);">{{ $image->category }}</span>
            @endif
            <span>{{ $image->file_size_human }}</span>
            <span>•</span>
            <span>{{ strtoupper(pathinfo($image->filename, PATHINFO_EXTENSION)) }}</span>
        </div>
    </header>

    {{-- Image viewer --}}
    <div style="
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg, 12px);
        overflow: hidden;
        background: var(--color-surface);
        padding: 1rem;
        text-align: center;
    ">
        <img src="{{ $image->url }}"
             alt="{{ $image->alt_text ?? $image->filename }}"
             style="max-width: 100%; height: auto; border-radius: var(--radius-md, 8px);"
             loading="lazy">
    </div>

    {{-- Metadata + caption --}}
    @if ($image->caption)
    <div class="mt-4" style="padding: 0.75rem 1.25rem; border: 1px solid var(--color-border); border-radius: var(--radius-lg, 12px); background: var(--color-surface);">
        <p class="text-sm" style="color: var(--color-text-muted);">{!! $image->caption !!}</p>
    </div>
    @endif

    {{-- See Also --}}
    <x-see-also :source="$image" :limit="6" />
</div>
@endsection

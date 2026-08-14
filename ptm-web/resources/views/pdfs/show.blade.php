@extends('layouts.app')

@section('content')
<div class="mx-auto" style="max-width: 50rem;">

    {{-- Breadcrumb --}}
    <nav class="mb-4 text-sm" style="color: var(--color-text-faint);">
        <a href="{{ route('home') }}" style="color: var(--color-text-faint);">Home</a>
        <span class="mx-1">›</span>
        <span style="color: var(--color-text-muted);">{{ $pdf->title ?? $pdf->filename }}</span>
    </nav>

    {{-- Header --}}
    <header class="mb-6">
        <h1 class="font-serif text-3xl font-bold mb-2" style="color: var(--color-text);">
            {{ $pdf->title ?? $pdf->filename }}
        </h1>
        @if ($pdf->description)
        <p class="text-sm" style="color: var(--color-text-muted);">{!! $pdf->description !!}</p>
        @endif
        <div class="flex items-center gap-3 mt-3 text-xs" style="color: var(--color-text-faint);">
            @if ($pdf->category)
            <span class="px-2 py-0.5 rounded-full" style="background: var(--color-surface-2, rgba(128,128,128,0.08)); border: 1px solid var(--color-border);">{{ $pdf->category }}</span>
            @endif
            <span>{{ $pdf->file_size_human }}</span>
            <span>•</span>
            <span>{{ strtoupper(pathinfo($pdf->filename, PATHINFO_EXTENSION)) }}</span>
        </div>
    </header>

    {{-- PDF viewer --}}
    <div style="
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg, 12px);
        overflow: hidden;
        background: var(--color-surface);
    ">
        <object data="{{ $pdf->url }}" type="application/pdf" style="width: 100%; height: 75vh; display: block;">
            <div style="padding: 2rem; text-align: center;">
                <p style="color: var(--color-text-muted); margin-bottom: 1rem;">
                    Your browser doesn't support inline PDF viewing.
                </p>
                <a href="{{ $pdf->url }}" download
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium"
                   style="background: var(--color-accent); color: var(--color-surface);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/>
                    </svg>
                    Download {{ $pdf->filename }}
                </a>
            </div>
        </object>
    </div>

    {{-- Download bar --}}
    <div class="mt-4 flex items-center justify-between" style="padding: 0.75rem 1.25rem; border: 1px solid var(--color-border); border-radius: var(--radius-lg, 12px); background: var(--color-surface);">
        <span class="text-sm" style="color: var(--color-text-muted);">{{ $pdf->filename }}</span>
        <a href="{{ $pdf->url }}" download
           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium"
           style="border: 1px solid var(--color-border); color: var(--color-text);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/>
            </svg>
            Download
        </a>
    </div>

    {{-- See Also --}}
    <x-see-also :source="$pdf" :limit="6" />
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-serif text-3xl font-bold" style="color: var(--color-text);">PDF Library</h1>
        <a href="{{ route('admin.pdfs.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm rounded-lg font-medium"
           style="background-color: var(--color-accent); color: var(--color-text-inv);">
            <svg width="16" height="20" viewBox="0 0 32 40" fill="none" style="display:block;">
                <path d="M4 0a4 4 0 0 0-4 4v32a4 4 0 0 0 4 4h24a4 4 0 0 0 4-4V12L20 0H4z" fill="currentColor"/>
                <path d="M20 0v8a4 4 0 0 0 4 4h8L20 0z" fill="currentColor" opacity="0.5"/>
                <rect x="6" y="16" width="20" height="18" rx="2" fill="#E9352F"/>
                <text x="16" y="29" text-anchor="middle" fill="white" font-family="Helvetica,Arial,sans-serif" font-size="8" font-weight="700" letter-spacing="0.5">PDF</text>
            </svg>
            Upload PDF
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg text-sm" style="background-color: var(--color-surface-2); border: 1px solid var(--color-accent); color: var(--color-accent);">
            {{ session('status') }}
        </div>
    @endif

    {{-- Search + Category filter --}}
    <form method="GET" class="flex flex-wrap items-center gap-3 mb-6">
        <input type="text" name="q" value="{{ request('q') }}"
               placeholder="Search PDFs..."
               class="flex-1 min-w-[12rem] h-9 px-3 border rounded-lg text-sm"
               style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
        <select name="category" class="h-9 px-3 border rounded-lg text-sm"
                style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            <option value="all">All categories</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>
        <button type="submit"
                class="h-9 px-4 border rounded-lg text-sm font-semibold"
                style="border-color: var(--color-accent); color: var(--color-accent); background: transparent;">
            Search
        </button>
        @if (request('q') || request('category'))
            <a href="{{ route('admin.pdfs.index') }}"
               class="text-sm" style="color: var(--color-text-muted);">Clear ×</a>
        @endif
    </form>

    @if (request('q'))
        <p class="text-sm mb-4" style="color: var(--color-text-muted);">
            Search results for &ldquo;<strong>{{ request('q') }}</strong>&rdquo; — {{ $pdfs->total() }} PDF(s) found
        </p>
    @endif

    {{-- PDF grid --}}
    @if ($pdfs->isNotEmpty())
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach ($pdfs as $pdf)
                <div class="pdf-card" style="border: 1px solid var(--color-border); border-radius: var(--radius-md); overflow: hidden;">
                    <div class="pdf-card__thumb" style="aspect-ratio: 1; background: var(--color-surface-2); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <svg width="48" height="60" viewBox="0 0 32 40" fill="none" style="display:block;">
                            <path d="M4 0a4 4 0 0 0-4 4v32a4 4 0 0 0 4 4h24a4 4 0 0 0 4-4V12L20 0H4z" fill="var(--color-surface)" stroke="var(--color-border)" stroke-width="1"/>
                            <path d="M20 0v8a4 4 0 0 0 4 4h8L20 0z" fill="var(--color-border)"/>
                            <rect x="6" y="16" width="20" height="18" rx="2" fill="#E9352F"/>
                            <text x="16" y="29" text-anchor="middle" fill="white" font-family="Helvetica,Arial,sans-serif" font-size="8" font-weight="700" letter-spacing="0.5">PDF</text>
                        </svg>
                    </div>
                    <div class="pdf-card__info" style="padding: 0.5rem;">
                        <p class="text-xs font-mono truncate" style="color: var(--color-text);" title="{{ $pdf->slug }}">{{ $pdf->slug }}</p>
                        <p class="text-xs truncate" style="color: var(--color-text-muted);">
                            {{ $pdf->file_size_human }}
                        </p>
                        @if ($pdf->category)
                            <span class="text-xs" style="color: var(--color-accent);">{{ $pdf->category }}</span>
                        @endif
                        <div class="flex items-center gap-2 mt-2">
                            <a href="{{ route('admin.pdfs.edit', $pdf) }}"
                               class="text-xs px-2 py-1 rounded border"
                               style="border-color: var(--color-border); color: var(--color-text);">Edit</a>
                            <form method="POST" action="{{ route('admin.pdfs.destroy', $pdf) }}"
                                  onsubmit="return confirm('Delete PDF {{ $pdf->slug }}? This removes it from disk.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs px-2 py-1 rounded border"
                                        style="border-color: var(--color-danger); color: var(--color-danger);">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $pdfs->withQueryString()->links() }}
        </div>
    @else
        <div style="padding: 3rem 2rem; text-align: center; color: var(--color-text-muted);">
            @if (request('q') || request('category'))
                <p>No PDFs found. <a href="{{ route('admin.pdfs.index') }}" style="color: var(--color-accent);">Clear search</a></p>
            @else
                <p>No PDFs uploaded yet. <a href="{{ route('admin.pdfs.create') }}" style="color: var(--color-accent);">Upload your first PDF</a></p>
            @endif
        </div>
    @endif
</div>
@endsection

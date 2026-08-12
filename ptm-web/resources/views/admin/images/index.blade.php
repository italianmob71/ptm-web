@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-serif text-3xl font-bold" style="color: var(--color-text);">Image Library</h1>
        <a href="{{ route('admin.images.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm rounded-lg font-medium"
           style="background-color: var(--color-accent); color: var(--color-text-inv);">
            <svg class="btn-icon" aria-hidden="true"><use xlink:href="#icon-book"></use></svg>
            Upload Images
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-lg text-sm" style="background-color: var(--color-surface-2); border: 1px solid var(--color-accent); color: var(--color-accent);">
            {{ session('success') }}
        </div>
    @endif

    {{-- Search + Category filter --}}
    <form method="GET" class="flex flex-wrap items-center gap-3 mb-6">
        <input type="text" name="q" value="{{ request('q') }}"
               placeholder="Search images..."
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
            <a href="{{ route('admin.images.index') }}"
               class="text-sm" style="color: var(--color-text-muted);">Clear ×</a>
        @endif
    </form>

    @if (request('q'))
        <p class="text-sm mb-4" style="color: var(--color-text-muted);">
            Search results for &ldquo;<strong>{{ request('q') }}</strong>&rdquo; — {{ $images->total() }} image(s) found
        </p>
    @endif

    {{-- Image grid --}}
    @if ($images->isNotEmpty())
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach ($images as $image)
                <div class="image-card" style="border: 1px solid var(--color-border); border-radius: var(--radius-md); overflow: hidden;">
                    <div class="image-card__thumb" style="aspect-ratio: 1; background: var(--color-surface-2); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <img src="{{ asset($image->path) }}" alt="{{ $image->alt_text ?: $image->slug }}"
                             style="max-width: 100%; max-height: 100%; object-fit: contain;"
                             loading="lazy">
                    </div>
                    <div class="image-card__info" style="padding: 0.5rem;">
                        <p class="text-xs font-mono truncate" style="color: var(--color-text);" title="{{ $image->slug }}">{{ $image->slug }}</p>
                        <p class="text-xs" style="color: var(--color-text-muted);">
                            {{ $image->file_size_human }}@if ($image->width) · {{ $image->width }}×{{ $image->height }}@endif
                        </p>
                        @if ($image->category)
                            <span class="text-xs" style="color: var(--color-accent);">{{ $image->category }}</span>
                        @endif
                        <div class="flex items-center gap-2 mt-2">
                            <a href="{{ route('admin.images.edit', $image) }}"
                               class="text-xs px-2 py-1 rounded border"
                               style="border-color: var(--color-border); color: var(--color-text);">Edit</a>
                            <form method="POST" action="{{ route('admin.images.destroy', $image) }}"
                                  onsubmit="return confirm('Delete image {{ $image->slug }}? This removes it from disk.');">
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
            {{ $images->withQueryString()->links() }}
        </div>
    @else
        <div style="padding: 3rem 2rem; text-align: center; color: var(--color-text-muted);">
            @if (request('q') || request('category'))
                <p>No images found. <a href="{{ route('admin.images.index') }}" style="color: var(--color-accent);">Clear search</a></p>
            @else
                <p>No images uploaded yet. <a href="{{ route('admin.images.create') }}" style="color: var(--color-accent);">Upload your first image</a></p>
            @endif
        </div>
    @endif
</div>
@endsection

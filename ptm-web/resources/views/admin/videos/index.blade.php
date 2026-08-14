@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-serif text-3xl font-bold" style="color: var(--color-text);">Videos</h1>
        <a href="{{ route('admin.videos.create') }}"
           class="px-4 py-2 text-sm rounded-lg font-medium"
           style="background-color: var(--color-accent); color: var(--color-text-inv);">
            + Add Video
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg text-sm" style="background-color: var(--color-surface-2); border: 1px solid var(--color-accent); color: var(--color-accent);">
            {{ session('status') }}
        </div>
    @endif

    {{-- Search + Category --}}
    <form method="GET" class="flex flex-wrap items-center gap-3 mb-6">
        <input type="text" name="q" value="{{ request('q') }}"
               placeholder="Search videos..."
               class="flex-1 min-w-[12rem] h-9 px-3 border rounded-lg text-sm"
               style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
        @if ($categories->isNotEmpty())
            <select name="category" class="h-9 px-3 border rounded-lg text-sm"
                    style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
                <option value="">All Categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        @endif
        <button type="submit"
                class="h-9 px-4 border rounded-lg text-sm font-semibold"
                style="border-color: var(--color-accent); color: var(--color-accent); background: transparent;">
            Search
        </button>
        @if (request('q') || request('category'))
            <a href="{{ route('admin.videos.index') }}"
               class="text-sm" style="color: var(--color-text-muted);">Clear ×</a>
        @endif
    </form>

    @if (request('q') || request('category'))
        <p class="text-sm mb-4" style="color: var(--color-text-muted);">
            {{ $videos->total() }} video(s) found
        </p>
    @endif

    {{-- Grid --}}
    @if ($videos->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach ($videos as $video)
                <div style="border: 1px solid var(--color-border); border-radius: var(--radius-md); overflow: hidden;">
                    {{-- Thumbnail: video poster or platform icon --}}
                    <div style="aspect-ratio: 16/9; background: var(--color-surface-2); display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
                        @if ($video->thumbnail_url)
                            <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}"
                                 style="max-width: 100%; max-height: 100%; object-fit: cover;"
                                 loading="lazy">
                        @else
                            {{-- Video icon --}}
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color: var(--color-text-faint);">
                                <rect x="2" y="4" width="20" height="16" rx="2"/>
                                <path d="M10 9l5 3-5 3V9z" fill="currentColor"/>
                            </svg>
                        @endif
                        {{-- Platform badge --}}
                        <span style="position: absolute; top: 0.5rem; right: 0.5rem; padding: 0.15rem 0.5rem; border-radius: var(--radius-sm); font-size: 0.65rem; font-weight: 600; text-transform: uppercase; background: rgba(0,0,0,0.6); color: #fff;">
                            {{ $video->source_platform }}
                        </span>
                    </div>
                    <div style="padding: 0.75rem;">
                        <p class="text-sm font-mono truncate" style="color: var(--color-text);" title="{{ $video->slug }}">{{ $video->slug }}</p>
                        <p class="text-sm font-semibold truncate" style="color: var(--color-text);" title="{{ $video->title }}">{{ $video->title }}</p>
                        @if ($video->category)
                            <p class="text-xs" style="color: var(--color-accent);">{{ $video->category }}</p>
                        @endif
                        <p class="text-xs" style="color: var(--color-text-faint);">
                            @if ($video->file_size_human){{ $video->file_size_human }} · @endif
                            @if ($video->published)
                                <span style="color: var(--color-accent);">Published</span>
                            @else
                                <span style="color: var(--color-danger);">Draft</span>
                            @endif
                        </p>
                        <div class="flex items-center gap-2 mt-2">
                            <a href="{{ route('admin.videos.edit', $video) }}"
                               class="text-xs px-2 py-1 rounded border"
                               style="border-color: var(--color-border); color: var(--color-text);">Edit</a>
                            <form method="POST" action="{{ route('admin.videos.destroy', $video) }}"
                                  onsubmit="return confirm('Delete video {{ $video->slug }}?');">
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

        <div class="mt-6">
            {{ $videos->withQueryString()->links() }}
        </div>
    @else
        <div style="padding: 3rem 2rem; text-align: center; color: var(--color-text-muted);">
            @if (request('q'))
                <p>No videos found. <a href="{{ route('admin.videos.index') }}" style="color: var(--color-accent);">Clear search</a></p>
            @else
                <p>No videos yet. <a href="{{ route('admin.videos.create') }}" style="color: var(--color-accent);">Add your first video</a></p>
            @endif
        </div>
    @endif
</div>
@endsection

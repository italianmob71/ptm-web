@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-serif text-3xl font-bold" style="color: var(--color-text);">Bryan's Travel Notes</h1>
        <a href="{{ route('admin.travel-notes.create') }}"
           class="px-4 py-2 text-sm rounded-lg font-medium"
           style="background-color: var(--color-accent); color: var(--color-text-inv);">
            + Add Travel Note
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg text-sm" style="background-color: var(--color-surface-2); border: 1px solid var(--color-accent); color: var(--color-accent);">
            {{ session('status') }}
        </div>
    @endif

    {{-- Search --}}
    <form method="GET" class="flex flex-wrap items-center gap-3 mb-6">
        <input type="text" name="q" value="{{ request('q') }}"
               placeholder="Search travel notes..."
               class="flex-1 min-w-[12rem] h-9 px-3 border rounded-lg text-sm"
               style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
        <button type="submit"
                class="h-9 px-4 border rounded-lg text-sm font-semibold"
                style="border-color: var(--color-accent); color: var(--color-accent); background: transparent;">
            Search
        </button>
        @if (request('q'))
            <a href="{{ route('admin.travel-notes.index') }}"
               class="text-sm" style="color: var(--color-text-muted);">Clear ×</a>
        @endif
    </form>

    @if (request('q'))
        <p class="text-sm mb-4" style="color: var(--color-text-muted);">
            Search results for &ldquo;<strong>{{ request('q') }}</strong>&rdquo; — {{ $notes->total() }} note(s) found
        </p>
    @endif

    {{-- Grid --}}
    @if ($notes->isNotEmpty())
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach ($notes as $note)
                <div style="border: 1px solid var(--color-border); border-radius: var(--radius-md); overflow: hidden;">
                    {{-- Thumbnail: teaser image or placeholder --}}
                    <div style="aspect-ratio: 1; background: var(--color-surface-2); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        @if ($note->teaserImage)
                            <img src="{{ $note->teaserImage->url }}" alt="{{ $note->title }}"
                                 style="max-width: 100%; max-height: 100%; object-fit: cover;"
                                 loading="lazy">
                        @else
                            <span class="text-xs" style="color: var(--color-text-faint);">No image</span>
                        @endif
                    </div>
                    <div style="padding: 0.5rem;">
                        <p class="text-xs font-mono truncate" style="color: var(--color-text);" title="{{ $note->slug }}">{{ $note->slug }}</p>
                        <p class="text-xs truncate" style="color: var(--color-text-muted);" title="{{ $note->title }}">{{ $note->title }}</p>
                        <p class="text-xs" style="color: var(--color-text-faint);">
                            Order: {{ $note->sort_order }}
                            @if ($note->published)
                                <span style="color: var(--color-accent);">· Published</span>
                            @else
                                <span style="color: var(--color-danger);">· Draft</span>
                            @endif
                        </p>
                        <div class="flex items-center gap-2 mt-2">
                            <a href="{{ route('admin.travel-notes.edit', $note) }}"
                               class="text-xs px-2 py-1 rounded border"
                               style="border-color: var(--color-border); color: var(--color-text);">Edit</a>
                            <form method="POST" action="{{ route('admin.travel-notes.destroy', $note) }}"
                                  onsubmit="return confirm('Delete travel note {{ $note->slug }}?');">
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
            {{ $notes->withQueryString()->links() }}
        </div>
    @else
        <div style="padding: 3rem 2rem; text-align: center; color: var(--color-text-muted);">
            @if (request('q'))
                <p>No travel notes found. <a href="{{ route('admin.travel-notes.index') }}" style="color: var(--color-accent);">Clear search</a></p>
            @else
                <p>No travel notes yet. <a href="{{ route('admin.travel-notes.create') }}" style="color: var(--color-accent);">Add your first travel note</a></p>
            @endif
        </div>
    @endif
</div>
@endsection

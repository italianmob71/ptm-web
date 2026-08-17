@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-serif text-3xl font-bold" style="color: var(--color-text);">Cochin Books</h1>
        <a href="{{ route('admin.cochin-books.create') }}"
           class="px-4 py-2 rounded-lg text-sm font-medium"
           style="background: var(--color-accent); color: var(--color-surface);">
            + Add New Book
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search books..."
               class="flex-1 h-9 px-3 border rounded-lg text-sm"
               style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
        <select name="status" class="h-9 px-3 border rounded-lg text-sm"
                style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            <option value="">All Statuses</option>
            <option value="wip" {{ request('status') === 'wip' ? 'selected' : '' }}>WIP</option>
            <option value="complete" {{ request('status') === 'complete' ? 'selected' : '' }}>Complete</option>
        </select>
        <button type="submit" class="h-9 px-4 border rounded-lg text-sm"
                style="border-color: var(--color-border); color: var(--color-text);">Search</button>
    </form>

    {{-- Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($books as $book)
        <div class="rounded-lg border p-4" style="border-color: var(--color-border); background: var(--color-surface);">
            <div class="flex items-start gap-3 mb-3">
                @if ($book->coverImage)
                <img src="{{ $book->coverImage->url }}" alt="{{ $book->title }}"
                     class="w-16 h-20 object-cover rounded">
                @else
                <div class="w-16 h-20 rounded flex items-center justify-center"
                     style="background: var(--color-surface-2, rgba(128,128,128,0.06)); border: 1px solid var(--color-border);">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--color-text-faint);">
                        <use href="#icon-book"></use>
                    </svg>
                </div>
                @endif
                <div class="flex-1 min-w-0">
                    <h3 class="font-serif text-lg font-bold truncate" style="color: var(--color-text);">
                        {{ $book->title }}
                    </h3>
                    @if ($book->manuscript)
                    <p class="text-xs" style="color: var(--color-text-faint);">{{ $book->manuscript }}</p>
                    @endif
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs px-2 py-0.5 rounded-full"
                              style="background: var(--color-surface-2, rgba(128,128,128,0.08)); border: 1px solid var(--color-border);">
                            {{ $book->is_wip ? 'WIP' : 'Complete' }}
                        </span>
                        @if ($book->published)
                        <span class="text-xs px-2 py-0.5 rounded-full" style="background: rgba(34,197,94,0.15); color: rgb(34,197,94);">Published</span>
                        @else
                        <span class="text-xs px-2 py-0.5 rounded-full" style="background: var(--color-surface-2, rgba(128,128,128,0.08)); color: var(--color-text-faint);">Draft</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Chapter progress --}}
            <div class="mb-3">
                <div class="flex items-center justify-between text-xs mb-1" style="color: var(--color-text-faint);">
                    <span>{{ $book->chapter_count }} / {{ $book->total_chapters }} chapters</span>
                    <span>{{ $book->progress_percent }}%</span>
                </div>
                <div style="height: 4px; border-radius: 2px; background: var(--color-surface-2, rgba(128,128,128,0.1)); overflow: hidden;">
                    <div style="height: 100%; width: {{ $book->progress_percent }}%; background: var(--color-accent); border-radius: 2px;"></div>
                </div>
            </div>

            <div class="flex gap-2 mt-3">
                <a href="{{ route('admin.cochin-books.edit', $book) }}"
                   class="flex-1 text-center py-1.5 rounded-lg text-sm border"
                   style="border-color: var(--color-border); color: var(--color-text);">Edit</a>
                <form method="POST" action="{{ route('admin.cochin-books.destroy', $book) }}"
                      onsubmit="return confirm('Delete {{ $book->title }}? This also deletes all chapters.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 rounded-lg text-sm border"
                            style="border-color: var(--color-danger); color: var(--color-danger);">Delete</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    {{ $books->links() }}
</div>
@endsection

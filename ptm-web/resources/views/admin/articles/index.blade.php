@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-serif text-3xl font-bold" style="color: var(--color-text);">Articles Dashboard</h1>
        <a href="{{ route('admin.articles.create') }}"
           class="px-4 py-2 text-sm rounded-lg font-medium"
           style="background-color: var(--color-accent); color: var(--color-text-inv);">
            + Add Article
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg" style="background-color: var(--color-surface-2); border: 1px solid var(--color-accent); color: var(--color-accent);">
            {{ session('status') }}
        </div>
    @endif

    {{-- Search --}}
    <form method="GET" action="{{ route('admin.articles.index') }}" class="mb-6">
        <div class="flex gap-2" style="max-width: 28rem;">
            <input type="text" name="q" value="{{ $search ?? '' }}"
                   placeholder="Search articles..."
                   class="flex-1 px-3 py-2 text-sm rounded-lg border"
                   style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text); height: 2.25rem;">
            <button type="submit"
                    class="px-4 py-2 text-sm rounded-lg"
                    style="background-color: var(--color-surface-2); border: 1px solid var(--color-border); color: var(--color-text);">
                Search
            </button>
            @if ($search)
                <a href="{{ route('admin.articles.index') }}"
                   class="px-3 py-2 text-sm rounded-lg"
                   style="color: var(--color-text-muted);">Clear &times;</a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm" style="color: var(--color-text);">
            <thead>
                <tr style="border-bottom: 2px solid var(--color-border); color: var(--color-text-muted);">
                    <th class="text-left py-3 px-2 font-medium">Title</th>
                    <th class="text-left py-3 px-2 font-medium">Author</th>
                    <th class="text-left py-3 px-2 font-medium">PDF</th>
                    <th class="text-left py-3 px-2 font-medium">Text</th>
                    <th class="text-left py-3 px-2 font-medium">Published</th>
                    <th class="text-left py-3 px-2 font-medium">Date</th>
                    <th class="text-left py-3 px-2 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($articles as $article)
                    <tr style="border-bottom: 1px solid var(--color-border-soft);">
                        <td class="py-3 px-2">
                            <div class="font-medium">{{ $article->title }}</div>
                            <div class="text-xs" style="color: var(--color-text-faint);">{{ $article->slug }}</div>
                        </td>
                        <td class="py-3 px-2">{{ $article->author?->full_name ?? '&mdash;' }}</td>
                        <td class="py-3 px-2">
                            @if ($article->is_pdf)
                                <span style="color: var(--color-success);">Yes</span>
                            @else
                                <span style="color: var(--color-text-faint);">&mdash;</span>
                            @endif
                        </td>
                        <td class="py-3 px-2">
                            @if ($article->is_full_text)
                                <span style="color: var(--color-success);">Yes</span>
                            @else
                                <span style="color: var(--color-text-faint);">&mdash;</span>
                            @endif
                        </td>
                        <td class="py-3 px-2">
                            @if ($article->published)
                                <span class="px-2 py-0.5 rounded text-xs" style="background-color: rgba(var(--color-success-rgb, 34, 197, 94), 0.15); color: var(--color-success);">Published</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs" style="background-color: var(--color-surface-2); color: var(--color-text-muted);">Draft</span>
                            @endif
                        </td>
                        <td class="py-3 px-2 text-xs" style="color: var(--color-text-muted);">
                            {{ $article->published_at?->format('M j, Y') ?? $article->created_at->format('M j, Y') }}
                        </td>
                        <td class="py-3 px-2">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.articles.edit', $article) }}"
                                   class="text-xs" style="color: var(--color-accent);">Edit</a>
                                <form method="POST" action="{{ route('admin.articles.destroy', $article) }}" onsubmit="return confirm('Delete this article?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs" style="color: var(--color-danger);">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-8" style="color: var(--color-text-muted);">
                            @if ($search)
                                No articles found for "{{ $search }}".
                            @else
                                No articles yet. Click "Add Article" to create one.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $articles->withQueryString()->links() }}
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-serif text-3xl font-bold" style="color: var(--color-text);">Blog Posts Dashboard</h1>
        <a href="{{ route('admin.blog.create') }}"
           class="px-4 py-2 text-sm rounded-lg font-medium"
           style="background-color: var(--color-accent); color: var(--color-text-inv);">
            + Add New Post
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg" style="background-color: var(--color-surface-2); border: 1px solid var(--color-border); color: var(--color-success);">
            {{ session('status') }}
        </div>
    @endif

    <div class="overflow-x-auto rounded-lg border" style="border-color: var(--color-border); background-color: var(--color-surface);">
        <table class="min-w-full text-sm">
            <thead style="background-color: var(--color-surface-2);">
                <tr>
                    <th class="text-left px-4 py-3 font-medium" style="color: var(--color-text-muted);">Title</th>
                    <th class="text-left px-4 py-3 font-medium" style="color: var(--color-text-muted);">Author</th>
                    <th class="text-left px-4 py-3 font-medium" style="color: var(--color-text-muted);">Published</th>
                    <th class="text-left px-4 py-3 font-medium" style="color: var(--color-text-muted);">Date</th>
                    <th class="text-right px-4 py-3 font-medium" style="color: var(--color-text-muted);">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr style="border-top: 1px solid var(--color-border-soft);">
                        <td class="px-4 py-3" style="color: var(--color-text);">
                            {{ Str::limit($post->title, 60) }}
                            <span class="block text-xs" style="color: var(--color-text-faint);">/{{ $post->slug }}</span>
                        </td>
                        <td class="px-4 py-3" style="color: var(--color-text-muted);">
                            {{ $post->author?->full_name ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @if ($post->published)
                                <span class="px-2 py-0.5 rounded text-xs font-medium"
                                      style="background-color: var(--color-success); color: var(--color-text-inv);">Yes</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs font-medium"
                                      style="background-color: var(--color-surface-3); color: var(--color-text-muted);">Draft</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs" style="color: var(--color-text-muted);">
                            {{ $post->published_at?->format('M j, Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('blog.show', $post->slug) }}"
                               class="inline-block px-3 py-1 text-xs rounded border"
                               style="border-color: var(--color-border); color: var(--color-text);"
                               target="_blank">View</a>
                            <a href="{{ route('admin.blog.edit', $post) }}"
                               class="inline-block px-3 py-1 text-xs rounded border"
                               style="border-color: var(--color-border); color: var(--color-text);">Edit</a>
                            <form method="POST" action="{{ route('admin.blog.destroy', $post) }}"
                                  class="inline-block"
                                  onsubmit="return confirm('Delete this blog post? This is a soft delete.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1 text-xs rounded border"
                                        style="border-color: var(--color-danger); color: var(--color-danger);">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center" style="color: var(--color-text-muted);">
                            No blog posts yet. Click "Add New Post" to create one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $posts->links() }}
</div>
@endsection

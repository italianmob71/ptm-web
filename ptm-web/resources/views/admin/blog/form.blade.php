@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8">
    <h1 class="font-serif text-3xl font-bold mb-6" style="color: var(--color-text);">
        {{ $post->exists ? 'Edit: ' . Str::limit($post->title, 50) : 'Add New Blog Post' }}
    </h1>

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg" style="background-color: var(--color-surface-2); border: 1px solid var(--color-danger); color: var(--color-danger);">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $post->exists ? route('admin.blog.update', $post) : route('admin.blog.store') }}"
          id="blog-post-form">
        @csrf
        @if ($post->exists)
            @method('PUT')
        @endif

        <!-- Title -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Title *</label>
            <input type="text" name="title" value="{{ old('title', $post->title) }}"
                   class="w-full px-3 py-2 rounded-lg border"
                   style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                   required>
        </div>

        <!-- Two-column: Slug and Author -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Slug <span class="text-xs" style="color: var(--color-text-faint);">(leave empty to auto-generate)</span></label>
                <input type="text" name="slug" value="{{ old('slug', $post->slug) }}"
                       class="w-full px-3 py-2 rounded-lg border font-mono text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       placeholder="auto-generated-from-title">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Author</label>
                <select name="author_id"
                        class="w-full px-3 py-2 rounded-lg border"
                        style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
                    <option value="">— No Author —</option>
                    @foreach ($authors as $author)
                        <option value="{{ $author->id }}" {{ old('author_id', $post->author_id) == $author->id ? 'selected' : '' }}>
                            {{ $author->full_name }}{{ $author->title ? ' (' . $author->title . ')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Excerpt -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Excerpt <span class="text-xs" style="color: var(--color-text-faint);">(auto-generated from content if left empty)</span></label>
            <textarea name="excerpt" rows="3"
                      class="w-full px-3 py-2 rounded-lg border"
                      style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">{{ old('excerpt', $post->excerpt) }}</textarea>
        </div>

        <!-- Content (CKEditor 5) -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Content *</label>
            <textarea name="content" id="editor"
                      class="w-full"
                      style="border-color: var(--color-border);">{{ old('content', $post->content) }}</textarea>
        </div>

        <!-- Featured Image -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Featured Image Filename <span class="text-xs" style="color: var(--color-text-faint);">(stored in public/images/site/)</span></label>
            <input type="text" name="featured_image" value="{{ old('featured_image', $post->featured_image) }}"
                   class="w-full px-3 py-2 rounded-lg border text-sm"
                   style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                   placeholder="e.g. revelation-500x500-1.jpg">
        </div>

        <!-- Two-column: Published checkbox and Published At date -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="flex items-center gap-2 text-sm" style="color: var(--color-text);">
                    <input type="checkbox" name="published" value="1" {{ old('published', $post->published) ? 'checked' : '' }}>
                    Published
                </label>
                <p class="text-xs mt-1" style="color: var(--color-text-faint);">Check to make this post visible on the site.</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Published At <span class="text-xs" style="color: var(--color-text-faint);">(leave empty for now)</span></label>
                <input type="datetime-local" name="published_at"
                       value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}"
                       class="w-full px-3 py-2 rounded-lg border"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-4">
            <button type="submit" id="submit-btn"
                    class="px-6 py-2 text-sm rounded-lg font-medium"
                    style="background-color: var(--color-accent); color: var(--color-text-inv);">
                {{ $post->exists ? 'Update Post' : 'Create Post' }}
            </button>
            <a href="{{ route('admin.blog.index') }}"
               class="text-sm"
               style="color: var(--color-text-muted);">Cancel</a>
        </div>
    </form>
</div>

<!-- CKEditor 5 — custom self-hosted PTM build -->
<link rel="stylesheet" href="{{ asset('css/ckeditor5.css') }}">
<script src="{{ asset('js/ptm-editor.js') }}"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
// Make CSRF token available for CKEditor upload adapter
window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('blog-post-form');
    const textarea = document.getElementById('editor');

    (window.PTMEditor.default || window.PTMEditor)
        .create(textarea, {
            licenseKey: 'GPL',
            ckfinder: {
                uploadUrl: '{{ route("admin.images.ckeditor") }}',
                requestHeaders: {
                    'X-CSRF-TOKEN': window.csrfToken
                }
            }
        })
        .then(editor => {
            window.ckeditorInstance = editor;
            form.addEventListener('submit', function () {
                textarea.value = editor.getData();
            });
        })
        .catch(error => {
            console.error('CKEditor init error:', error);
        });
});
</script>

<style>
/* CKEditor 5 dark theme integration */
.ck.ck-toolbar {
    background: var(--color-surface-2) !important;
    border-color: var(--color-border) !important;
    border-radius: var(--radius-md) var(--radius-md) 0 0 !important;
}
.ck.ck-toolbar button {
    color: var(--color-text-muted) !important;
}
.ck.ck-toolbar button.ck-on,
.ck.ck-toolbar button:hover {
    color: var(--color-accent) !important;
}
.ck.ck-toolbar .ck.ck-dropdown__panel {
    background: var(--color-surface) !important;
    border-color: var(--color-border) !important;
}
.ck.ck-editor__editable {
    background-color: var(--color-bg) !important;
    color: var(--color-text) !important;
    border-color: var(--color-border) !important;
    border-radius: 0 0 var(--radius-md) var(--radius-md) !important;
    min-height: 400px;
}
.ck.ck-editor__editable:focus {
    border-color: var(--color-accent) !important;
}
.ck.ck-content {
    font-family: var(--font-serif) !important;
    font-size: 1rem !important;
    line-height: 1.8 !important;
    color: var(--color-text) !important;
    padding: 1.5rem !important;
}
</style>
@endsection

@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8">
    <h1 class="font-serif text-3xl font-bold mb-6" style="color: var(--color-text);">
        {{ $article->exists ? 'Edit: ' . Str::limit($article->title, 50) : 'Add New Article' }}
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

    <form method="POST" action="{{ $article->exists ? route('admin.articles.update', $article) : route('admin.articles.store') }}"
          id="article-form">
        @csrf
        @if ($article->exists)
            @method('PUT')
        @endif

        <!-- Title -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Title *</label>
            <input type="text" name="title" value="{{ old('title', $article->title) }}"
                   class="w-full px-3 py-2 rounded-lg border"
                   style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                   required>
        </div>

        <!-- Sub-title -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Sub-title <span class="text-xs" style="color: var(--color-text-faint);">(optional, shown below the title)</span></label>
            <input type="text" name="sub_title" value="{{ old('sub_title', $article->sub_title) }}"
                   class="w-full px-3 py-2 rounded-lg border"
                   style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                   placeholder="A secondary title or tagline">
        </div>

        <!-- Two-column: Slug and Author -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Slug <span class="text-xs" style="color: var(--color-text-faint);">(leave empty to auto-generate)</span></label>
                <input type="text" name="slug" value="{{ old('slug', $article->slug) }}"
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
                        <option value="{{ $author->id }}" {{ old('author_id', $article->author_id) == $author->id ? 'selected' : '' }}>
                            {{ $author->full_name }}{{ $author->title ? ' (' . $author->title . ')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Summary -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Summary <span class="text-xs" style="color: var(--color-text-faint);">(shown on the article listing page)</span></label>
            <textarea name="summary" rows="3"
                      class="w-full px-3 py-2 rounded-lg border"
                      style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">{{ old('summary', $article->summary) }}</textarea>
        </div>

        <!-- Content (CKEditor 5) -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Content <span class="text-xs" style="color: var(--color-text-faint);">(use the toolbar to insert images and PDFs)</span></label>
            <textarea name="content" id="editor"
                      class="w-full"
                      style="border-color: var(--color-border);">{{ old('content', $article->content) }}</textarea>
        </div>

        <!-- NOSEARCH + Published row -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div>
                <label class="flex items-center gap-2 text-sm" style="color: var(--color-text);">
                    <input type="checkbox" name="nosearch" value="1"
                           {{ old('nosearch', str_starts_with($article->slug ?? '', 'NOSEARCH-')) ? 'checked' : '' }}>
                    NOSEARCH
                </label>
                <p class="text-xs mt-1" style="color: var(--color-text-faint);">Excludes from site search (slug gets NOSEARCH- prefix).</p>
            </div>
            <div>
                <label class="flex items-center gap-2 text-sm" style="color: var(--color-text);">
                    <input type="checkbox" name="published" value="1" {{ old('published', $article->published) ? 'checked' : '' }}>
                    Published
                </label>
                <p class="text-xs mt-1" style="color: var(--color-text-faint);">Check to make this article visible.</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Published At</label>
                <input type="datetime-local" name="published_at"
                       value="{{ old('published_at', $article->published_at?->format('Y-m-d\TH:i')) }}"
                       class="w-full px-3 py-2 rounded-lg border text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-4">
            <button type="submit" id="submit-btn"
                    class="px-6 py-2 text-sm rounded-lg font-medium"
                    style="background-color: var(--color-accent); color: var(--color-text-inv);">
                {{ $article->exists ? 'Update Article' : 'Create Article' }}
            </button>
            <a href="{{ route('admin.articles.index') }}"
               class="text-sm"
               style="color: var(--color-text-muted);">Cancel</a>
        </div>
    </form>
</div>

<!-- CKEditor 5 — custom self-hosted PTM build (same as blog, with imagePicker + pdfPicker) -->
<link rel="stylesheet" href="{{ asset('css/ckeditor5.css') }}">
<script src="{{ asset('js/ptm-editor.js') }}"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('article-form');
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
/* CKEditor 5 theme integration — same as blog form */
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
    min-height: 200px;
    max-height: 50vh;
    overflow-y: auto;
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
/* Scripture quote style — indented italic with left accent border */
.ck.ck-content blockquote {
    border-left: 4px solid var(--color-accent) !important;
    padding: 0.5rem 1rem !important;
    margin: 1rem 0 !important;
    font-style: italic !important;
    color: var(--color-text-muted) !important;
    background: var(--color-surface-2) !important;
    border-radius: 0 var(--radius-sm) var(--radius-sm) 0 !important;
}
.ck.ck-content blockquote p {
    margin: 0.25rem 0 !important;
}
/* Images in tables — shrink to fit cell */
.ck.ck-content td img {
    max-width: 100% !important;
    height: auto !important;
}
.ck.ck-content table { table-layout: auto !important; }
</style>
@endsection

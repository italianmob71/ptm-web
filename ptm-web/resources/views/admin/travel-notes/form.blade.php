@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8">
    <h1 class="font-serif text-3xl font-bold mb-6" style="color: var(--color-text);">
        {{ $note->exists ? 'Edit: ' . Str::limit($note->title, 50) : 'Add New Travel Note' }}
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

    <form method="POST" action="{{ $note->exists ? route('admin.travel-notes.update', $note) : route('admin.travel-notes.store') }}"
          id="travel-note-form">
        @csrf
        @if ($note->exists)
            @method('PUT')
        @endif

        <!-- Title -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Title *</label>
            <input type="text" name="title" value="{{ old('title', $note->title) }}"
                   class="w-full px-3 py-2 rounded-lg border"
                   style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                   required>
        </div>

        <!-- Author -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Author <span class="text-xs" style="color: var(--color-text-faint);">(whose bio appears at the bottom)</span></label>
            <select name="author_id"
                    class="w-full px-3 py-2 rounded-lg border"
                    style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
                <option value="">— Select Author —</option>
                @foreach ($authors as $author)
                    <option value="{{ $author->id }}" {{ old('author_id', $note->author_id) == $author->id ? 'selected' : '' }}>
                        {{ $author->full_name }}@if($author->title) — {{ $author->title }}@endif
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Two-column: Slug and Sort Order -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Slug <span class="text-xs" style="color: var(--color-text-faint);">(leave empty to auto-generate)</span></label>
                <input type="text" name="slug" value="{{ old('slug', $note->slug) }}"
                       class="w-full px-3 py-2 rounded-lg border font-mono text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       placeholder="auto-generated-from-title">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Sort Order <span class="text-xs" style="color: var(--color-text-faint);">(lower = earlier in tour)</span></label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $note->sort_order) }}"
                       class="w-full px-3 py-2 rounded-lg border text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            </div>
        </div>

        <!-- Two-column: Biblical Reference and Location -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Biblical Reference <span class="text-xs" style="color: var(--color-text-faint);">(optional)</span></label>
                <input type="text" name="biblical_reference" value="{{ old('biblical_reference', $note->biblical_reference) }}"
                       class="w-full px-3 py-2 rounded-lg border text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       placeholder="e.g. Exodus 2:15-16">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Location <span class="text-xs" style="color: var(--color-text-faint);">(optional)</span></label>
                <input type="text" name="location" value="{{ old('location', $note->location) }}"
                       class="w-full px-3 py-2 rounded-lg border text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       placeholder="e.g. Al-bad, Saudi Arabia">
            </div>
        </div>

        <!-- Teaser Image Picker -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Teaser Image <span class="text-xs" style="color: var(--color-text-faint);">(shown on the listing page — pick from the Image Library)</span></label>
            <div class="flex items-center gap-3">
                <div id="teaser-preview" style="width: 4rem; height: 4rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm); overflow: hidden; background: var(--color-surface-2); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    @if ($note->teaserImage)
                        <img src="{{ $note->teaserImage->url }}" alt="Teaser" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <span style="font-size: 0.7rem; color: var(--color-text-faint);">None</span>
                    @endif
                </div>
                <input type="hidden" name="teaser_image_id" id="teaser-image-id" value="{{ old('teaser_image_id', $note->teaser_image_id) }}">
                <button type="button" id="teaser-pick-btn"
                        class="px-3 py-2 text-sm rounded-lg border"
                        style="border-color: var(--color-accent); color: var(--color-accent); background: transparent;">
                    Pick Image
                </button>
                <button type="button" id="teaser-clear-btn"
                        class="px-3 py-2 text-sm rounded-lg border"
                        style="border-color: var(--color-border); color: var(--color-text-muted); background: transparent;">
                    Remove
                </button>
            </div>
        </div>

        <!-- Content (CKEditor 5) -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Content <span class="text-xs" style="color: var(--color-text-faint);">(use toolbar to insert images and PDFs)</span></label>
            <textarea name="content" id="editor"
                      class="w-full"
                      style="border-color: var(--color-border);">{{ old('content', $note->content) }}</textarea>
        </div>

        <!-- Published row -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="flex items-center gap-2 text-sm" style="color: var(--color-text);">
                    <input type="checkbox" name="published" value="1" {{ old('published', $note->published) ? 'checked' : '' }}>
                    Published
                </label>
                <p class="text-xs mt-1" style="color: var(--color-text-faint);">Check to make this note visible.</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Published At</label>
                <input type="datetime-local" name="published_at"
                       value="{{ old('published_at', $note->published_at?->format('Y-m-d\TH:i')) }}"
                       class="w-full px-3 py-2 rounded-lg border text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-4">
            <button type="submit" id="submit-btn"
                    class="px-6 py-2 text-sm rounded-lg font-medium"
                    style="background-color: var(--color-accent); color: var(--color-text-inv);">
                {{ $note->exists ? 'Update Note' : 'Create Note' }}
            </button>
            <a href="{{ route('admin.travel-notes.index') }}"
               class="text-sm" style="color: var(--color-text-muted);">Cancel</a>
        </div>
    </form>
</div>

<!-- Teaser Image Picker Modal -->
<div id="teaser-picker-overlay" style="display:none; position:fixed; inset:0; z-index:10000; background:rgba(0,0,0,0.6); display:none; align-items:flex-start; justify-content:center; padding-top:5vh; overflow-y:auto;">
    <div id="teaser-picker-modal" style="background:var(--color-surface,#fff); border:1px solid var(--color-border,#ddd); border-radius:12px; width:90%; max-width:720px; max-height:88vh; overflow-y:auto; box-shadow:0 8px 32px rgba(0,0,0,0.3);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.5rem;border-bottom:1px solid var(--color-border,#ddd);position:sticky;top:0;background:var(--color-surface,#fff);z-index:1;">
            <h3 style="margin:0;font-family:var(--font-serif,serif);font-size:1.25rem;color:var(--color-text,#333);">Pick Teaser Image</h3>
            <button id="teaser-picker-close" style="background:none;border:none;font-size:1.5rem;cursor:pointer;color:var(--color-text-muted,#999);">×</button>
        </div>
        <div style="display:flex;border-bottom:1px solid var(--color-border,#ddd);">
            <button class="tp-tab" data-tab="search" style="flex:1;padding:0.75rem;background:none;border:none;cursor:pointer;font-size:0.875rem;font-weight:600;border-bottom:3px solid var(--color-accent,#c5a572);color:var(--color-text,#333);">Search Library</button>
            <button class="tp-tab" data-tab="url" style="flex:1;padding:0.75rem;background:none;border:none;cursor:pointer;font-size:0.875rem;font-weight:600;border-bottom:3px solid transparent;color:var(--color-text-muted,#999);">URL</button>
        </div>
        <div style="padding:1.5rem;">
            <!-- Search tab -->
            <div class="tp-panel" data-panel="search">
                <div style="display:flex;gap:0.5rem;margin-bottom:1rem;">
                    <input type="text" id="tp-search-input" placeholder="Search images by name, alt text, or category..." style="flex:1;padding:0.5rem 0.75rem;border:1px solid var(--color-border,#ddd);border-radius:6px;font-size:0.8125rem;background:var(--color-surface,#fff);color:var(--color-text,#333);">
                    <button id="tp-search-btn" style="padding:0.5rem 1rem;background:none;border:1px solid var(--color-accent,#c5a572);color:var(--color-accent,#c5a572);border-radius:6px;font-size:0.8125rem;font-weight:600;cursor:pointer;">Search</button>
                </div>
                <div id="tp-search-results" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:0.75rem;max-height:400px;overflow-y:auto;"></div>
            </div>
            <!-- URL tab -->
            <div class="tp-panel" data-panel="url" style="display:none;">
                <div style="margin-bottom:1rem;">
                    <label style="display:block;font-size:0.8125rem;color:var(--color-text,#333);margin-bottom:0.5rem;">Image URL</label>
                    <input type="text" id="tp-url-input" placeholder="https://example.com/image.jpg" style="width:100%;padding:0.5rem 0.75rem;border:1px solid var(--color-border,#ddd);border-radius:6px;font-size:0.8125rem;background:var(--color-surface,#fff);color:var(--color-text,#333);">
                </div>
                <button id="tp-url-btn" style="padding:0.5rem 1.5rem;background:var(--color-accent,#c5a572);color:var(--color-text-inv,#fff);border:none;border-radius:6px;font-size:0.8125rem;font-weight:600;cursor:pointer;">Use This URL</button>
                <p style="margin-top:0.5rem;font-size:0.75rem;color:var(--color-text-faint,#ccc);">Note: URL images are not stored in the library. They load directly from the source.</p>
            </div>
        </div>
    </div>
</div>

<!-- CKEditor 5 -->
<link rel="stylesheet" href="{{ asset('css/ckeditor5.css') }}">
<script src="{{ asset('js/ptm-editor.js') }}"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('travel-note-form');
    const textarea = document.getElementById('editor');

    (window.PTMEditor.default || window.PTMEditor)
        .create(textarea, {
            licenseKey: 'GPL',
            ckfinder: {
                uploadUrl: '{{ route("admin.images.ckeditor") }}',
                requestHeaders: { 'X-CSRF-TOKEN': window.csrfToken }
            }
        })
        .then(editor => {
            window.ckeditorInstance = editor;
            form.addEventListener('submit', function () {
                textarea.value = editor.getData();
            });
        })
        .catch(error => { console.error('CKEditor init error:', error); });
});
</script>

<!-- Teaser Image Picker Logic -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('teaser-picker-overlay');
    const modal = document.getElementById('teaser-picker-modal');
    const pickBtn = document.getElementById('teaser-pick-btn');
    const clearBtn = document.getElementById('teaser-clear-btn');
    const closeBtn = document.getElementById('teaser-picker-close');
    const idInput = document.getElementById('teaser-image-id');
    const preview = document.getElementById('teaser-preview');
    const urlInput = document.getElementById('tp-url-input');

    let usingUrl = false; // if true, we store the URL directly instead of an image_id

    function openModal() {
        overlay.style.display = 'flex';
        doSearch();
    }
    function closeModal() { overlay.style.display = 'none'; }

    pickBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', (e) => { if (e.target === overlay) closeModal(); });

    clearBtn.addEventListener('click', () => {
        idInput.value = '';
        usingUrl = false;
        urlInput.value = '';
        preview.innerHTML = '<span style="font-size:0.7rem;color:var(--color-text-faint);">None</span>';
    });

    // Tab switching
    modal.querySelectorAll('.tp-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.tab;
            modal.querySelectorAll('.tp-tab').forEach(t => {
                t.style.borderBottomColor = 'transparent';
                t.style.color = 'var(--color-text-muted,#999)';
            });
            tab.style.borderBottomColor = 'var(--color-accent,#c5a572)';
            tab.style.color = 'var(--color-text,#333)';
            modal.querySelectorAll('.tp-panel').forEach(p => {
                p.style.display = p.dataset.panel === target ? 'block' : 'none';
            });
        });
    });

    // Search
    const searchInput = document.getElementById('tp-search-input');
    const searchBtn = document.getElementById('tp-search-btn');
    const searchResults = document.getElementById('tp-search-results');

    function doSearch() {
        const q = searchInput.value.trim();
        const url = q ? '/admin/images/search?q=' + encodeURIComponent(q) : '/admin/images/search';
        searchResults.innerHTML = '<p style="color:var(--color-text-muted,#999);font-size:0.8125rem;">Searching...</p>';
        fetch(url).then(r => r.json()).then(images => {
            if (images.length === 0) {
                searchResults.innerHTML = '<p style="color:var(--color-text-muted,#999);font-size:0.8125rem;">No images found.</p>';
                return;
            }
            searchResults.innerHTML = '';
            images.forEach(img => {
                const card = document.createElement('div');
                card.style.cssText = 'border:1px solid var(--color-border,#ddd);border-radius:8px;overflow:hidden;text-align:center;cursor:pointer;';
                card.innerHTML = '<div style="aspect-ratio:1;background:var(--color-surface-2,#f5f5f5);overflow:hidden;"><img src="' + img.url + '" style="max-width:100%;max-height:100%;object-fit:contain;" loading="lazy"></div><div style="padding:0.3rem;"><p style="font-size:0.65rem;font-family:monospace;color:var(--color-text,#333);margin:0 0 0.2rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + img.slug + '</p><button style="width:100%;padding:0.2rem;background:var(--color-accent,#c5a572);color:var(--color-text-inv,#fff);border:none;border-radius:4px;font-size:0.65rem;font-weight:600;cursor:pointer;">Use</button></div>';
                card.querySelector('button').addEventListener('click', (e) => {
                    e.stopPropagation();
                    idInput.value = img.id;
                    usingUrl = false;
                    preview.innerHTML = '<img src="' + img.url + '" alt="Teaser" style="width:100%;height:100%;object-fit:cover;">';
                    closeModal();
                });
                searchResults.appendChild(card);
            });
        }).catch(err => {
            searchResults.innerHTML = '<p style="color:var(--color-danger,#dc2626);font-size:0.8125rem;">Error: ' + err.message + '</p>';
        });
    }

    searchBtn.addEventListener('click', doSearch);
    searchInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); doSearch(); } });

    // URL tab
    document.getElementById('tp-url-btn').addEventListener('click', () => {
        const url = document.getElementById('tp-url-input').value.trim();
        if (!url) return;
        // Store URL in a data attr — we'll handle this server-side as a special case
        // For now, we use a negative ID convention: store the URL in the hidden field prefixed with 'url:'
        idInput.value = 'url:' + url;
        usingUrl = true;
        preview.innerHTML = '<img src="' + url + '" alt="Teaser" style="width:100%;height:100%;object-fit:cover;">';
        closeModal();
    });
});
</script>

<style>
.ck.ck-toolbar {
    background: var(--color-surface-2) !important;
    border-color: var(--color-border) !important;
    border-radius: var(--radius-md) var(--radius-md) 0 0 !important;
}
.ck.ck-toolbar button { color: var(--color-text-muted) !important; }
.ck.ck-toolbar button.ck-on, .ck.ck-toolbar button:hover { color: var(--color-accent) !important; }
.ck.ck-toolbar .ck.ck-dropdown__panel { background: var(--color-surface) !important; border-color: var(--color-border) !important; }
.ck.ck-editor__editable {
    background-color: var(--color-bg) !important;
    color: var(--color-text) !important;
    border-color: var(--color-border) !important;
    border-radius: 0 0 var(--radius-md) var(--radius-md) !important;
    min-height: 200px;
    max-height: 50vh;
    overflow-y: auto;
}
.ck.ck-editor__editable:focus { border-color: var(--color-accent) !important; }
.ck.ck-content {
    font-family: var(--font-serif) !important;
    font-size: 1rem !important;
    line-height: 1.8 !important;
    color: var(--color-text) !important;
    padding: 1.5rem !important;
}
.ck.ck-content blockquote {
    border-left: 4px solid var(--color-accent) !important;
    padding: 0.5rem 1rem !important;
    margin: 1rem 0 !important;
    font-style: italic !important;
    color: var(--color-text-muted) !important;
    background: var(--color-surface-2) !important;
    border-radius: 0 var(--radius-sm) var(--radius-sm) 0 !important;
}
.ck.ck-content blockquote p { margin: 0.25rem 0 !important; }
.ck.ck-content td img {
    max-width: 100% !important;
    height: auto !important;
}
.ck.ck-content table { table-layout: auto !important; }
</style>
@endsection

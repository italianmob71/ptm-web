@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8">
    <h1 class="font-serif text-3xl font-bold mb-6" style="color: var(--color-text);">
        {{ $book->exists ? "Edit: {$book->title}" : 'Add New Cochin Book' }}
    </h1>

    @if (session('success'))
    <div class="mb-4 p-3 rounded-lg" style="background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); color: rgb(34,197,94);">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="mb-4 p-3 rounded-lg" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: rgb(239,68,68);">
        @foreach ($errors->all() as $error)
        <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    {{-- ── BOOK DETAILS FORM ───────────────────────────────── --}}
    <form id="book-form" method="POST" action="{{ $book->exists ? route('admin.cochin-books.update', $book) : route('admin.cochin-books.store') }}"
          enctype="multipart/form-data">
        @csrf @method($book->exists ? 'PUT' : 'POST')

        <div class="rounded-lg border p-6 mb-6" style="border-color: var(--color-border); background: var(--color-surface);">
            <h2 class="font-serif text-xl font-bold mb-4" style="color: var(--color-text);">Book Details</h2>

            {{-- Title --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Title *</label>
                <input type="text" name="title" value="{{ old('title', $book->title) }}"
                       class="w-full h-9 px-3 border rounded-lg text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       placeholder="e.g. Cochin Hebrew Matthew" required>
            </div>

            {{-- Slug --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Slug <span class="text-xs" style="color: var(--color-text-faint);">(auto-generated if blank)</span></label>
                <input type="text" name="slug" value="{{ old('slug', $book->slug) }}"
                       class="w-full h-9 px-3 border rounded-lg text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       placeholder="matthew">
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                {{-- Manuscript --}}
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Manuscript</label>
                    <input type="text" name="manuscript" value="{{ old('manuscript', $book->manuscript) }}"
                           class="w-full h-9 px-3 border rounded-lg text-sm"
                           style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                           placeholder="MS Oo.1.32">
                </div>

                {{-- Total chapters --}}
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Total Chapters</label>
                    <input type="number" name="total_chapters" value="{{ old('total_chapters', $book->total_chapters) }}"
                           class="w-full h-9 px-3 border rounded-lg text-sm"
                           style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                           min="0" max="200" placeholder="28">
                </div>
            </div>

            {{-- Display order (canonical NT order, auto-derived from slug) --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Display Order <span class="text-xs" style="color: var(--color-text-faint);">(canonical NT order — auto-set from slug, override if needed)</span></label>
                <input type="number" name="display_order" value="{{ old('display_order', $book->display_order ?? 999) }}"
                       class="w-full h-9 px-3 border rounded-lg text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       min="1" max="999" placeholder="999">
            </div>

            {{-- Status --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Status</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 text-sm" style="color: var(--color-text);">
                        <input type="radio" name="status" value="wip"
                               {{ old('status', $book->status) === 'wip' ? 'checked' : '' }}>
                        Work in Progress
                    </label>
                    <label class="flex items-center gap-2 text-sm" style="color: var(--color-text);">
                        <input type="radio" name="status" value="complete"
                               {{ old('status', $book->status) === 'complete' ? 'checked' : '' }}>
                        Complete
                    </label>
                </div>
            </div>

            {{-- Cover image --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Cover Image</label>
                <select name="cover_image_id"
                        class="w-full h-9 px-3 border rounded-lg text-sm"
                        style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
                    <option value="">— None —</option>
                    @foreach ($images as $img)
                    <option value="{{ $img->id }}" {{ old('cover_image_id', $book->cover_image_id) == $img->id ? 'selected' : '' }}>
                        {{ $img->alt_text ?? $img->filename }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Complete PDF (for finished books) --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Complete Book PDF <span class="text-xs" style="color: var(--color-text-faint);">(only for complete books)</span></label>
                <select name="complete_pdf_id"
                        class="w-full h-9 px-3 border rounded-lg text-sm"
                        style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
                    <option value="">— None —</option>
                    @foreach ($pdfs as $pdf)
                    <option value="{{ $pdf->id }}" {{ old('complete_pdf_id', $book->complete_pdf_id) == $pdf->id ? 'selected' : '' }}>
                        {{ $pdf->title ?? $pdf->filename }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Description (CKEditor) --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Description / Introduction</label>
                <textarea name="description" id="description-editor" rows="10"
                          class="w-full border rounded-lg text-sm p-3"
                          style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">{{ old('description', $book->description) }}</textarea>
            </div>

            {{-- Discoveries (CKEditor) --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Key Discoveries <span class="text-xs" style="color: var(--color-text-faint);">(shown on Discoveries tab)</span></label>
                <textarea name="discoveries" id="discoveries-editor" rows="10"
                          class="w-full border rounded-lg text-sm p-3"
                          style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">{{ old('discoveries', $book->discoveries) }}</textarea>
            </div>

            {{-- Published --}}
            <div class="mb-4">
                <label class="flex items-center gap-2 text-sm" style="color: var(--color-text);">
                    <input type="checkbox" name="published" {{ old('published', $book->published) ? 'checked' : '' }}>
                    Published <span class="text-xs" style="color: var(--color-text-faint);">(visible on public site)</span>
                </label>
            </div>

            <button type="submit" class="px-6 py-2 rounded-lg text-sm font-medium"
                    style="background: var(--color-accent); color: var(--color-surface);">
                {{ $book->exists ? 'Update Book' : 'Create Book' }}
            </button>
        </div>
    </form>

    {{-- ── CHAPTERS SECTION (only when editing existing book) ─────── --}}
    @if ($book->exists)
    <div class="rounded-lg border p-6" style="border-color: var(--color-border); background: var(--color-surface);">
        <h2 class="font-serif text-xl font-bold mb-4" style="color: var(--color-text);">
            Chapters <span class="text-sm font-normal" style="color: var(--color-text-faint);">({{ $book->chapters->count() }} / {{ $book->total_chapters }})</span>
        </h2>

        {{-- Add chapter form with drag-and-drop PDF upload --}}
        <form id="chapter-add-form" method="POST" action="{{ route('admin.cochin-books.chapters.store', $book) }}" enctype="multipart/form-data" class="mb-6">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-xs font-medium mb-1" style="color: var(--color-text-faint);">Chapter #</label>
                    <input type="number" name="chapter_number" min="1" max="200"
                           class="w-full h-9 px-3 border rounded-lg text-sm"
                           style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                           value="{{ old('chapter_number') }}" required>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1" style="color: var(--color-text-faint);">Title (optional)</label>
                    <input type="text" name="title"
                           class="w-full h-9 px-3 border rounded-lg text-sm"
                           style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                           value="{{ old('title') }}" placeholder="Chapter 1">
                </div>
            </div>

            {{-- PDF: drag-drop upload OR select existing --}}
            <div class="mb-3">
                <label class="block text-xs font-medium mb-1" style="color: var(--color-text-faint);">PDF</label>
                <div class="flex gap-3 items-start">
                    {{-- Upload a new PDF --}}
                    <div class="flex-1">
                        <div id="chapter-pdf-dropzone" class="border-2 border-dashed rounded-lg p-4 text-center cursor-pointer transition-colors"
                             style="border-color: var(--color-border); background: var(--color-surface-2, rgba(128,128,128,0.03));"
                             onclick="document.getElementById('chapter-pdf-upload').click()">
                            <svg class="w-6 h-6 mx-auto mb-1" style="color: var(--color-text-faint);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 010-8 6 6 0 0111.5-2A3.5 3.5 0 0119 14"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12v8m-4-4l4 4 4-4"/>
                            </svg>
                            <p class="text-xs" style="color: var(--color-text-faint);">Drop PDF here or click to upload</p>
                            <p id="chapter-pdf-name" class="text-xs mt-1 font-medium" style="color: var(--color-accent); display: none;"></p>
                        </div>
                        <input type="file" id="chapter-pdf-upload" name="chapter_pdf" accept="application/pdf" class="hidden">
                    </div>

                    {{-- OR select existing --}}
                    <div class="w-40">
                        <select name="pdf_id"
                                class="w-full h-9 px-3 border rounded-lg text-xs"
                                style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
                            <option value="">— OR pick existing —</option>
                            @foreach ($pdfs as $pdf)
                            <option value="{{ $pdf->id }}">{{ $pdf->title ?? $pdf->filename }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-3">
                <label class="flex items-center gap-2 text-sm" style="color: var(--color-text);">
                    <input type="checkbox" name="published" checked>
                    Published
                </label>
                <button type="submit" class="px-4 py-1.5 rounded-lg text-sm font-medium"
                        style="background: var(--color-accent); color: var(--color-surface);">
                    + Add Chapter
                </button>
            </div>
        </form>

        {{-- Chapter list --}}
        @if ($book->chapters->count() > 0)
        <div class="space-y-2">
            @foreach ($book->chapters as $chapter)
            <div class="flex items-center gap-3 p-3 rounded-lg" style="background: var(--color-surface-2, rgba(128,128,128,0.04)); border: 1px solid var(--color-border);">
                <span class="font-bold text-lg" style="color: var(--color-text); min-width: 2rem; text-align: center;">{{ $chapter->chapter_number }}</span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate" style="color: var(--color-text);">
                        {{ $chapter->display_title }}
                    </p>
                    <div class="flex items-center gap-2 mt-0.5">
                        @if ($chapter->pdf)
                        <span class="text-xs flex items-center gap-1" style="color: var(--color-text-faint);">
                            <svg style="width:0.75rem; height:0.75rem; color: #E9352F;"><use href="#icon-pdf"></use></svg>
                            {{ $chapter->pdf->filename }} ({{ $chapter->pdf->file_size_human ?? '' }})
                        </span>
                        @endif
                        @if ($chapter->published)
                        <span class="text-xs px-1.5 py-0.5 rounded" style="background: rgba(34,197,94,0.15); color: rgb(34,197,94);">Published</span>
                        @else
                        <span class="text-xs px-1.5 py-0.5 rounded" style="color: var(--color-text-faint);">Draft</span>
                        @endif
                    </div>
                </div>
                {{-- Inline edit: change PDF link, toggle published --}}
                <form method="POST" action="{{ route('admin.cochin-books.chapters.update', [$book, $chapter]) }}" class="flex items-center gap-1">
                    @csrf @method('PUT')
                    <input type="hidden" name="chapter_number" value="{{ $chapter->chapter_number }}">
                    <input type="hidden" name="title" value="{{ $chapter->title }}">
                    <select name="pdf_id" class="h-8 px-2 border rounded text-xs"
                            style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
                        <option value="">— PDF —</option>
                        @foreach ($pdfs as $pdf)
                        <option value="{{ $pdf->id }}" {{ $chapter->pdf_id == $pdf->id ? 'selected' : '' }}>{{ $pdf->title ?? $pdf->filename }}</option>
                        @endforeach
                    </select>
                    <label class="flex items-center gap-1 text-xs" style="color: var(--color-text);">
                        <input type="checkbox" name="published" {{ $chapter->published ? 'checked' : '' }}>
                        Pub
                    </label>
                    <button type="submit" class="px-2 py-1 rounded text-xs border"
                            style="border-color: var(--color-border); color: var(--color-text);">Save</button>
                </form>
                <form method="POST" action="{{ route('admin.cochin-books.chapters.destroy', [$book, $chapter]) }}"
                      onsubmit="return confirm('Remove chapter {{ $chapter->chapter_number }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-2 py-1 rounded text-xs border"
                            style="border-color: var(--color-danger); color: var(--color-danger);">×</button>
                </form>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm" style="color: var(--color-text-faint);">No chapters yet. Add the first one above.</p>
        @endif
    </div>
    @endif
</div>

{{-- CKEditor init — matches blog form pattern exactly --}}
<link rel="stylesheet" href="{{ asset('css/ckeditor5.css') }}">
<script src="{{ asset('js/ptm-editor.js') }}"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
// Make CSRF token available for CKEditor upload adapter
window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
document.addEventListener('DOMContentLoaded', function () {
    const EditorClass = window.PTMEditor.default || window.PTMEditor;
    if (!EditorClass) {
        console.error('PTMEditor not loaded');
        return;
    }

    // Each CKEditor instance needs its own config object (CKEditor mutates it)
    function makeConfig() {
        return {
            licenseKey: 'GPL',
            ckfinder: {
                uploadUrl: '{{ route("admin.images.ckeditor") }}',
                requestHeaders: {
                    'X-CSRF-TOKEN': window.csrfToken
                }
            }
        };
    }

    // Description editor
    const descTextarea = document.getElementById('description-editor');
    if (descTextarea) {
        EditorClass.create(descTextarea, makeConfig())
            .then(editor => {
                // Sync editor data back to textarea on form submit
                document.getElementById('book-form').addEventListener('submit', function () {
                    descTextarea.value = editor.getData();
                });
            })
            .catch(err => console.error('CKEditor init error (description):', err));
    }

    // Discoveries editor
    const discTextarea = document.getElementById('discoveries-editor');
    if (discTextarea) {
        EditorClass.create(discTextarea, makeConfig())
            .then(editor => {
                // Sync editor data back to textarea on form submit
                document.getElementById('book-form').addEventListener('submit', function () {
                    discTextarea.value = editor.getData();
                });
            })
            .catch(err => console.error('CKEditor init error (discoveries):', err));
    }

    // Drag-and-drop PDF upload for chapter form
    const dropzone = document.getElementById('chapter-pdf-dropzone');
    const fileInput = document.getElementById('chapter-pdf-upload');
    const fileNameDisplay = document.getElementById('chapter-pdf-name');

    if (dropzone && fileInput) {
        ['dragenter', 'dragover'].forEach(evt => {
            dropzone.addEventListener(evt, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.style.borderColor = 'var(--color-accent)';
                dropzone.style.background = 'rgba(255,255,255,0.03)';
            });
        });
        ['dragleave', 'drop'].forEach(evt => {
            dropzone.addEventListener(evt, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.style.borderColor = 'var(--color-border)';
                dropzone.style.background = 'var(--color-surface-2, rgba(128,128,128,0.03))';
            });
        });
        dropzone.addEventListener('drop', function(e) {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                fileNameDisplay.textContent = files[0].name;
                fileNameDisplay.style.display = 'block';
            }
        });
        fileInput.addEventListener('change', function() {
            if (fileInput.files.length > 0) {
                fileNameDisplay.textContent = fileInput.files[0].name;
                fileNameDisplay.style.display = 'block';
            }
        });
    }
});
</script>

<style>
/* CKEditor 5 theme integration — matches blog form exactly */
.ck.ck-toolbar {
    background: var(--color-surface-2, rgba(128,128,128,0.04)) !important;
    border-color: var(--color-border) !important;
    border-radius: var(--radius-md, 8px) var(--radius-md, 8px) 0 0 !important;
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
    border-radius: 0 0 var(--radius-md, 8px) var(--radius-md, 8px) !important;
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
.ck.ck-content td img { max-width: 100% !important; height: auto !important; }
.ck.ck-content table { table-layout: auto !important; }
</style>
@endsection

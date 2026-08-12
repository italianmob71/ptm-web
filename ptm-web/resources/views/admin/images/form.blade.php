@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8">
    <h1 class="font-serif text-3xl font-bold mb-6" style="color: var(--color-text);">
        {{ $image->exists ? 'Edit Image: ' . Str::limit($image->slug, 50) : 'Upload Images' }}
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

    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-lg text-sm" style="background-color: var(--color-surface-2); border: 1px solid var(--color-accent); color: var(--color-accent);">
            {{ session('success') }}
        </div>
    @endif

    @if (!$image->exists)
        {{-- Upload form --}}
        <form method="POST" action="{{ route('admin.images.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-medium mb-2" style="color: var(--color-text);">Select Image(s) *</label>
                <div id="drop-zone" style="
                    border: 2px dashed var(--color-border);
                    border-radius: var(--radius-md);
                    padding: 2rem;
                    text-align: center;
                    cursor: pointer;
                    background: var(--color-surface);
                    transition: border-color 0.2s ease, background 0.2s ease;
                ">
                    <p style="color: var(--color-text-muted); font-size: 0.875rem;">
                        Click or drag images here to upload
                    </p>
                    <p style="color: var(--color-text-faint); font-size: 0.75rem; margin-top: 0.5rem;">
                        JPG, PNG, GIF, WebP, SVG — max 10MB each
                    </p>
                    <input type="file" name="files[]" id="file-input" multiple
                           accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml"
                           style="display: none;">
                </div>
                <div id="file-list" class="mt-3 space-y-1"></div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Category <span class="text-xs" style="color: var(--color-text-faint);">(optional — for grouping)</span></label>
                <input type="text" name="category" value="{{ old('category') }}"
                       list="category-list"
                       class="w-full h-9 px-3 border rounded-lg text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       placeholder="e.g. blog-featured, author-photos, book-covers">
                <datalist id="category-list">
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}">
                    @endforeach
                </datalist>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Alt Text <span class="text-xs" style="color: var(--color-text-faint);">(optional — applied to all uploads)</span></label>
                <input type="text" name="alt_text" value="{{ old('alt_text') }}"
                       class="w-full h-9 px-3 border rounded-lg text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       placeholder="Descriptive text for accessibility/SEO">
            </div>

            <div class="flex items-center gap-4">
                <button type="submit"
                        class="px-6 py-2 text-sm rounded-lg font-medium"
                        style="background-color: var(--color-accent); color: var(--color-text-inv);">
                    Upload
                </button>
                <a href="{{ route('admin.images.index') }}"
                   class="text-sm" style="color: var(--color-text-muted);">Cancel</a>
            </div>
        </form>

        <script>
        // Drag-and-drop + file picker
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');
        const fileList = document.getElementById('file-list');

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = 'var(--color-accent)';
            dropZone.style.background = 'var(--color-surface-2)';
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.style.borderColor = 'var(--color-border)';
            dropZone.style.background = 'var(--color-surface)';
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = 'var(--color-border)';
            dropZone.style.background = 'var(--color-surface)';
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                showFileList();
            }
        });

        fileInput.addEventListener('change', showFileList);

        function showFileList() {
            fileList.innerHTML = '';
            const files = fileInput.files;
            if (files.length === 0) return;
            Array.from(files).forEach((f) => {
                const sizeMB = (f.size / 1048576).toFixed(1);
                const p = document.createElement('p');
                p.textContent = `${f.name} (${sizeMB} MB)`;
                p.className = 'text-xs';
                p.style.color = 'var(--color-text-muted)';
                fileList.appendChild(p);
            });
        }
        </script>
    @else
        {{-- Edit form --}}
        <form method="POST" action="{{ route('admin.images.update', $image) }}">
            @csrf
            @method('PUT')

            {{-- Preview --}}
            <div class="mb-6" style="border: 1px solid var(--color-border); border-radius: var(--radius-md); overflow: hidden; text-align: center; background: var(--color-surface-2);">
                <img src="{{ asset($image->path) }}" alt="{{ $image->alt_text ?: $image->slug }}"
                     style="max-width: 100%; max-height: 24rem; object-fit: contain;">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Slug *</label>
                <input type="text" name="slug" value="{{ old('slug', $image->slug) }}"
                       class="w-full h-9 px-3 border rounded-lg text-sm font-mono"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       required>
                <p class="text-xs mt-1" style="color: var(--color-text-faint);">Changing the slug renames the file on disk.</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Filename</label>
                    <input type="text" value="{{ $image->filename }}" disabled
                           class="w-full h-9 px-3 border rounded-lg text-sm font-mono"
                           style="border-color: var(--color-border); background-color: var(--color-surface-2); color: var(--color-text-muted);">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">File Size</label>
                    <input type="text" value="{{ $image->file_size_human }} · {{ $image->mime_type }}@if ($image->width) · {{ $image->width }}×{{ $image->height }}@endif" disabled
                           class="w-full h-9 px-3 border rounded-lg text-sm"
                           style="border-color: var(--color-border); background-color: var(--color-surface-2); color: var(--color-text-muted);">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Alt Text</label>
                <input type="text" name="alt_text" value="{{ old('alt_text', $image->alt_text) }}"
                       class="w-full h-9 px-3 border rounded-lg text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       placeholder="Descriptive text for accessibility/SEO">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Caption</label>
                <input type="text" name="caption" value="{{ old('caption', $image->caption) }}"
                       class="w-full h-9 px-3 border rounded-lg text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       placeholder="Caption displayed with the image">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Category</label>
                <input type="text" name="category" value="{{ old('category', $image->category) }}"
                       list="category-list"
                       class="w-full h-9 px-3 border rounded-lg text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       placeholder="e.g. blog-featured, author-photos">
                <datalist id="category-list">
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}">
                    @endforeach
                </datalist>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit"
                        class="px-6 py-2 text-sm rounded-lg font-medium"
                        style="background-color: var(--color-accent); color: var(--color-text-inv);">
                    Save Changes
                </button>
                <a href="{{ route('admin.images.index') }}"
                   class="text-sm" style="color: var(--color-text-muted);">Cancel</a>
            </div>
        </form>
    @endif
</div>
@endsection

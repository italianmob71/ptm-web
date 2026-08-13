@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8">
    <h1 class="font-serif text-3xl font-bold mb-6" style="color: var(--color-text);">
        {{ $pdf->exists ? 'Edit PDF: ' . Str::limit($pdf->slug, 50) : 'Upload PDF' }}
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

    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg text-sm" style="background-color: var(--color-surface-2); border: 1px solid var(--color-accent); color: var(--color-accent);">
            {{ session('status') }}
        </div>
    @endif

    @if (!$pdf->exists)
        {{-- Upload form --}}
        <form method="POST" action="{{ route('admin.pdfs.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Upload / URL toggle --}}
            <div class="mb-6">
                <label class="block text-sm font-medium mb-2" style="color: var(--color-text);">PDF Source *</label>
                <div class="flex gap-4 mb-3">
                    <label class="flex items-center gap-2 text-sm" style="color: var(--color-text);">
                        <input type="radio" name="pdf_type" value="upload"
                               {{ old('pdf_type', 'upload') === 'upload' ? 'checked' : '' }}
                               onchange="document.getElementById('upload-zone').classList.remove('hidden'); document.getElementById('url-zone').classList.add('hidden');">
                        Upload from computer
                    </label>
                    <label class="flex items-center gap-2 text-sm" style="color: var(--color-text);">
                        <input type="radio" name="pdf_type" value="url"
                               {{ old('pdf_type') === 'url' ? 'checked' : '' }}
                               onchange="document.getElementById('upload-zone').classList.add('hidden'); document.getElementById('url-zone').classList.remove('hidden');">
                        Import from URL
                    </label>
                </div>

                <!-- Upload zone -->
                <div id="upload-zone" class="{{ old('pdf_type', 'upload') === 'upload' ? '' : 'hidden' }}">
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
                            Click or drag a PDF here to upload
                        </p>
                        <p style="color: var(--color-text-faint); font-size: 0.75rem; margin-top: 0.5rem;">
                            PDF files only — max 50MB
                        </p>
                        <input type="file" name="pdf_file" id="file-input"
                               accept="application/pdf,.pdf"
                               style="display: none;">
                    </div>
                    <div id="file-list" class="mt-3 space-y-1"></div>
                </div>

                <!-- URL zone -->
                <div id="url-zone" class="{{ old('pdf_type') === 'url' ? '' : 'hidden' }}">
                    <input type="url" name="pdf_url" value="{{ old('pdf_url') }}"
                           placeholder="https://example.com/document.pdf"
                           class="w-full h-9 px-3 border rounded-lg text-sm"
                           style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
                    <p class="text-xs mt-1" style="color: var(--color-text-faint);">Downloads the PDF to our server.</p>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Title <span class="text-xs" style="color: var(--color-text-faint);">(optional — display name)</span></label>
                <input type="text" name="title" value="{{ old('title') }}"
                       class="w-full h-9 px-3 border rounded-lg text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       placeholder="e.g. The Cochin Hebrew Manuscripts: A Preliminary Analysis">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Category <span class="text-xs" style="color: var(--color-text-faint);">(optional — for grouping)</span></label>
                <input type="text" name="category" value="{{ old('category') }}"
                       list="category-list"
                       class="w-full h-9 px-3 border rounded-lg text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       placeholder="e.g. Manuscript Studies, Special Studies">
                <datalist id="category-list">
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}">
                    @endforeach
                </datalist>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Description <span class="text-xs" style="color: var(--color-text-faint);">(optional)</span></label>
                <textarea name="description" rows="3"
                          class="w-full px-3 py-2 border rounded-lg text-sm"
                          style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                          placeholder="Short description of this PDF...">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit"
                        class="px-6 py-2 text-sm rounded-lg font-medium"
                        style="background-color: var(--color-accent); color: var(--color-text-inv);">
                    Upload
                </button>
                <a href="{{ route('admin.pdfs.index') }}"
                   class="text-sm" style="color: var(--color-text-muted);">Cancel</a>
            </div>
        </form>

        <script>
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
                p.textContent = f.name + ' (' + sizeMB + ' MB)';
                p.className = 'text-xs';
                p.style.color = 'var(--color-text-muted)';
                fileList.appendChild(p);
            });
        }
        </script>
    @else
        {{-- Edit form --}}
        <form method="POST" action="{{ route('admin.pdfs.update', $pdf) }}">
            @csrf
            @method('PUT')

            {{-- Preview --}}
            <div class="mb-6" style="border: 1px solid var(--color-border); border-radius: var(--radius-md); overflow: hidden; text-align: center; background: var(--color-surface-2); padding: 2rem;">
                <svg width="64" height="80" viewBox="0 0 32 40" fill="none" style="display:block; margin: 0 auto;">
                    <path d="M4 0a4 4 0 0 0-4 4v32a4 4 0 0 0 4 4h24a4 4 0 0 0 4-4V12L20 0H4z" fill="var(--color-surface)" stroke="var(--color-border)" stroke-width="1"/>
                    <path d="M20 0v8a4 4 0 0 0 4 4h8L20 0z" fill="var(--color-border)"/>
                    <rect x="6" y="16" width="20" height="18" rx="2" fill="#E9352F"/>
                    <text x="16" y="29" text-anchor="middle" fill="white" font-family="Helvetica,Arial,sans-serif" font-size="8" font-weight="700" letter-spacing="0.5">PDF</text>
                </svg>
                <p class="text-sm mt-3" style="color: var(--color-text-muted);">
                    <a href="{{ $pdf->url }}" target="_blank" style="color: var(--color-accent);">Open PDF &rarr;</a>
                </p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Slug *</label>
                <input type="text" name="slug" value="{{ old('slug', $pdf->slug) }}"
                       class="w-full h-9 px-3 border rounded-lg text-sm font-mono"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       required>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Filename</label>
                    <input type="text" value="{{ $pdf->filename }}" disabled
                           class="w-full h-9 px-3 border rounded-lg text-sm font-mono"
                           style="border-color: var(--color-border); background-color: var(--color-surface-2); color: var(--color-text-muted);">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">File Size</label>
                    <input type="text" value="{{ $pdf->file_size_human }} · {{ $pdf->mime_type }}" disabled
                           class="w-full h-9 px-3 border rounded-lg text-sm"
                           style="border-color: var(--color-border); background-color: var(--color-surface-2); color: var(--color-text-muted);">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Title</label>
                <input type="text" name="title" value="{{ old('title', $pdf->title) }}"
                       class="w-full h-9 px-3 border rounded-lg text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       placeholder="Display name for this PDF">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Description</label>
                <textarea name="description" rows="3"
                          class="w-full px-3 py-2 border rounded-lg text-sm"
                          style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                          placeholder="Short description of this PDF...">{{ old('description', $pdf->description) }}</textarea>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Category</label>
                <input type="text" name="category" value="{{ old('category', $pdf->category) }}"
                       list="category-list"
                       class="w-full h-9 px-3 border rounded-lg text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       placeholder="e.g. Manuscript Studies, Special Studies">
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
                <a href="{{ route('admin.pdfs.index') }}"
                   class="text-sm" style="color: var(--color-text-muted);">Cancel</a>
            </div>
        </form>
    @endif
</div>
@endsection

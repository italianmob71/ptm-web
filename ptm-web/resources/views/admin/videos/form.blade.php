@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8">
    <h1 class="font-serif text-3xl font-bold mb-6" style="color: var(--color-text);">
        {{ $video->exists ? 'Edit: ' . Str::limit($video->title, 50) : 'Add New Video' }}
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

    <form method="POST" action="{{ $video->exists ? route('admin.videos.update', $video) : route('admin.videos.store') }}"
          enctype="multipart/form-data">
        @csrf
        @if ($video->exists)
            @method('PUT')
        @endif

        @if (!$video->exists)
        <!-- Upload / URL toggle -->
        <div class="mb-6">
            <label class="block text-sm font-medium mb-2" style="color: var(--color-text);">Video Source *</label>
            <div class="flex gap-4 mb-3">
                <label class="flex items-center gap-2 text-sm" style="color: var(--color-text);">
                    <input type="radio" name="source_mode" value="upload" checked
                           onchange="document.getElementById('upload-zone').classList.remove('hidden'); document.getElementById('url-zone').classList.add('hidden');">
                    Upload from computer
                </label>
                <label class="flex items-center gap-2 text-sm" style="color: var(--color-text);">
                    <input type="radio" name="source_mode" value="url"
                           onchange="document.getElementById('upload-zone').classList.add('hidden'); document.getElementById('url-zone').classList.remove('hidden');">
                    Paste URL (YouTube / Rumble)
                </label>
            </div>

            <!-- Upload zone -->
            <div id="upload-zone">
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
                        Click or drag a video here to upload
                    </p>
                    <p style="color: var(--color-text-faint); font-size: 0.75rem; margin-top: 0.5rem;">
                        MP4, MOV, WebM — max 500MB
                    </p>
                    <input type="file" name="video_file" id="video-file-input"
                           accept="video/mp4,video/quicktime,video/webm"
                           style="display: none;">
                </div>
                <div id="video-file-list" class="mt-3 space-y-1"></div>
            </div>

            <!-- URL zone (hidden by default) -->
            <div id="url-zone" class="hidden">
                <input type="text" name="source_url" value="{{ old('source_url') }}"
                       class="w-full h-9 px-3 border rounded-lg text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       placeholder="https://youtube.com/watch?v=... or https://rumble.com/...">
                <p class="text-xs mt-1" style="color: var(--color-text-faint);">The system will auto-detect the platform and extract the video ID.</p>
            </div>
        </div>
        @endif

        <!-- Title -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Title *</label>
            <input type="text" name="title" value="{{ old('title', $video->title) }}"
                   class="w-full px-3 py-2 rounded-lg border"
                   style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                   required>
        </div>

        <!-- Slug -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Slug <span class="text-xs" style="color: var(--color-text-faint);">(leave empty to auto-generate)</span></label>
            <input type="text" name="slug" value="{{ old('slug', $video->slug) }}"
                   class="w-full px-3 py-2 rounded-lg border font-mono text-sm"
                   style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                   placeholder="auto-generated">
        </div>

        @if ($video->exists)
        <!-- Edit mode: show source info -->
        @if ($video->is_local)
        <div class="mb-4 p-3 rounded-lg" style="background: var(--color-surface-2); border: 1px solid var(--color-border);">
            <p class="text-xs" style="color: var(--color-text-muted);">Local file: <strong style="color: var(--color-text);">{{ $video->filename }}</strong> · {{ $video->file_size_human }}</p>
        </div>
        @elseif ($video->source_url)
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Source URL ({{ $video->source_platform }})</label>
            <input type="text" name="source_url" value="{{ old('source_url', $video->source_url) }}"
                   class="w-full px-3 py-2 rounded-lg border text-sm"
                   style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
        </div>
        @endif
        @endif

        <!-- Category -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Category <span class="text-xs" style="color: var(--color-text-faint);">(optional)</span></label>
            <input type="text" name="category" value="{{ old('category', $video->category) }}"
                   list="video-categories"
                   class="w-full px-3 py-2 rounded-lg border text-sm"
                   style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            <datalist id="video-categories">
                <option value="Cochin">
                <option value="Travel Notes">
                <option value="Teaching">
                <option value="Lecture">
                <option value="Conference">
            </datalist>
        </div>

        <!-- Description -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Description <span class="text-xs" style="color: var(--color-text-faint);">(optional)</span></label>
            <textarea name="description" rows="3"
                      class="w-full px-3 py-2 rounded-lg border"
                      style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">{{ old('description', $video->description) }}</textarea>
        </div>

        <!-- Published -->
        <div class="mb-6">
            <label class="flex items-center gap-2 text-sm" style="color: var(--color-text);">
                <input type="checkbox" name="published" value="1" {{ old('published', $video->published) ? 'checked' : '' }}>
                Published
            </label>
            <p class="text-xs mt-1" style="color: var(--color-text-faint);">Check to make this video visible.</p>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-4">
            <button type="submit"
                    class="px-6 py-2 text-sm rounded-lg font-medium"
                    style="background-color: var(--color-accent); color: var(--color-text-inv);">
                {{ $video->exists ? 'Save Changes' : 'Add Video' }}
            </button>
            <a href="{{ route('admin.videos.index') }}"
               class="text-sm" style="color: var(--color-text-muted);">Cancel</a>
        </div>
    </form>
</div>

<script>
// Drag-and-drop for video upload
(function() {
    const dropZone = document.getElementById('drop-zone');
    if (!dropZone) return;

    const fileInput = document.getElementById('video-file-input');
    const fileList = document.getElementById('video-file-list');

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
        for (let i = 0; i < files.length; i++) {
            const f = files[i];
            const sizeMB = (f.size / 1024 / 1024).toFixed(1);
            const item = document.createElement('div');
            item.style.cssText = 'padding: 0.5rem 0.75rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-size: 0.8125rem; color: var(--color-text);';
            item.innerHTML = '<strong>' + f.name + '</strong> <span style="color: var(--color-text-muted);">(' + sizeMB + ' MB)</span>';
            fileList.appendChild(item);
        }
    }
})();
</script>
@endsection

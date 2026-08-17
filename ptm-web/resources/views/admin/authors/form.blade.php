@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8">
    <h1 class="font-serif text-3xl font-bold mb-6" style="color: var(--color-text);">
        {{ $author->exists ? 'Edit: ' . $author->full_name : 'Add New Author' }}
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

    <form method="POST" action="{{ $author->exists ? route('admin.authors.update', $author) : route('admin.authors.store') }}"
          id="author-form">
        @csrf
        @if ($author->exists)
            @method('PUT')
        @endif

        <!-- Two-column: First Name and Last Name -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">First Name *</label>
                <input type="text" name="first_name" value="{{ old('first_name', $author->first_name) }}"
                       class="w-full px-3 py-2 rounded-lg border"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Last Name *</label>
                <input type="text" name="last_name" value="{{ old('last_name', $author->last_name) }}"
                       class="w-full px-3 py-2 rounded-lg border"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       required>
            </div>
        </div>

        <!-- Two-column: Title and Middle Initial -->
        <div class="grid grid-cols-3 gap-4 mb-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Title <span class="text-xs" style="color: var(--color-text-faint);">(e.g. Founder, Director, Researcher)</span></label>
                <input type="text" name="title" value="{{ old('title', $author->title) }}"
                       maxlength="255"
                       class="w-full px-3 py-2 rounded-lg border"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       placeholder="e.g. Founder & Director">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Middle Initial</label>
                <input type="text" name="middle_initial" value="{{ old('middle_initial', $author->middle_initial) }}"
                       maxlength="5"
                       class="w-full px-3 py-2 rounded-lg border text-center font-mono"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            </div>
        </div>

        <!-- Bio (CKEditor 5) -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Bio <span class="text-xs" style="color: var(--color-text-faint);">(rich text with toolbar)</span></label>
            <textarea name="bio" id="bio-editor"
                      class="w-full"
                      style="border-color: var(--color-border);">{{ old('bio', $author->bio) }}</textarea>
        </div>

        <!-- Image filename -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Image Filename <span class="text-xs" style="color: var(--color-text-faint);">(stored in public/images/authors/)</span></label>
            <input type="text" name="image" value="{{ old('image', $author->image) }}"
                   class="w-full px-3 py-2 rounded-lg border text-sm"
                   style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                   placeholder="e.g. jane-doe.jpg">
        </div>

        <!-- Priority -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Priority <span class="text-xs" style="color: var(--color-text-faint);">(0 = first, lower numbers appear first on team page)</span></label>
            <input type="number" name="priority" value="{{ old('priority', $author->priority ?? 0) }}"
                   min="0" max="65535"
                   class="w-full px-3 py-2 rounded-lg border"
                   style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
        </div>

        {{-- Social Media Links --}}
        <div class="mb-4">
            <h3 class="font-serif text-lg font-semibold mb-3" style="color: var(--color-text);">Social Media Links</h3>
            <p class="text-xs mb-3" style="color: var(--color-text-faint);">Leave empty if the author doesn't have an account on that platform.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @php
                    $socials = [
                        'facebook'    => 'Facebook',
                        'youtube'     => 'YouTube',
                        'rumble'      => 'Rumble',
                        'linkedin'    => 'LinkedIn',
                        'truthsocial' => 'Truth Social',
                        'tiktok'       => 'TikTok',
                        'x'             => 'X',
                    ];
                @endphp
                @foreach ($socials as $field => $label)
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">
                            <svg class="btn-icon" aria-hidden="true" style="vertical-align: middle; margin-right: 0.3rem; fill: var(--color-text-muted);">
                                <use xlink:href="#icon-{{ $field }}"></use>
                            </svg>
                            {{ $label }}
                        </label>
                        <input type="url" name="{{ $field }}" value="{{ old($field, $author->$field) }}"
                               class="w-full px-3 py-2 rounded-lg border text-sm"
                               style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                               placeholder="https://{{ $field }}.com/username">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Checkboxes -->
        <div class="flex gap-6 mb-6">
            <label class="flex items-center gap-2 text-sm" style="color: var(--color-text);">
                <input type="checkbox" name="active" value="1" {{ old('active', $author->active ?? true) ? 'checked' : '' }}>
                Active
            </label>
            <label class="flex items-center gap-2 text-sm" style="color: var(--color-text);">
                <input type="checkbox" name="team_member" value="1" {{ old('team_member', $author->team_member) ? 'checked' : '' }}>
                Team Member
            </label>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-4">
            <button type="submit"
                    class="px-6 py-2 text-sm rounded-lg font-medium"
                    style="background-color: var(--color-accent); color: var(--color-text-inv);">
                {{ $author->exists ? 'Update Author' : 'Create Author' }}
            </button>
            <a href="{{ route('admin.authors.index') }}"
               class="text-sm"
               style="color: var(--color-text-muted);">Cancel</a>
        </div>
    </form>
</div>

{{-- CKEditor 5 — custom self-hosted PTM build --}}
<link rel="stylesheet" href="{{ asset('css/ckeditor5.css') }}">
<script src="{{ asset('js/ptm-editor.js') }}"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('author-form');
    const textarea = document.getElementById('bio-editor');

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
/* CKEditor 5 theme integration — matches blog/articles forms */
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
    min-height: 150px;
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
    padding: 1rem !important;
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
.ck.ck-content blockquote p {
    margin: 0.25rem 0 !important;
}
.ck.ck-content td img {
    max-width: 100% !important;
    height: auto !important;
}
.ck.ck-content table { table-layout: auto !important; }
</style>
@endsection

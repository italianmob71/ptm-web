@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8">
    <h1 class="font-serif text-3xl font-bold mb-6" style="color: var(--color-text);">
        {{ $book->exists ? 'Edit: ' . $book->title : 'Add New Book' }}
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

    <form method="POST" action="{{ $book->exists ? route('admin.books.update', $book) : route('admin.books.store') }}">
        @csrf
        @if ($book->exists)
            @method('PUT')
        @endif

        <!-- Title -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Title *</label>
            <input type="text" name="title" value="{{ old('title', $book->title) }}"
                   class="w-full px-3 py-2 rounded-lg border"
                   style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                   required>
        </div>

        <!-- Subtitle -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Subtitle</label>
            <input type="text" name="subtitle" value="{{ old('subtitle', $book->subtitle) }}"
                   class="w-full px-3 py-2 rounded-lg border"
                   style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
        </div>

        <!-- Slug -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Slug <span class="text-xs" style="color: var(--color-text-faint);">(leave blank to auto-generate from title)</span></label>
            <input type="text" name="slug" value="{{ old('slug', $book->slug) }}"
                   class="w-full px-3 py-2 rounded-lg border font-mono text-sm"
                   style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
        </div>

        <!-- Author -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Author *</label>
            <select name="author_id"
                    class="w-full px-3 py-2 rounded-lg border"
                    style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                    required>
                @foreach ($authors as $author)
                    <option value="{{ $author->id }}" {{ (string) old('author_id', $book->author_id) === (string) $author->id ? 'selected' : '' }}>
                        {{ $author->full_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Body -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Body / Description</label>
            <textarea name="body" rows="6"
                      class="w-full px-3 py-2 rounded-lg border"
                      style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">{{ old('body', $book->body) }}</textarea>
        </div>

        <!-- Two-column: ISBN-13 and ISBN-10 -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">ISBN-13</label>
                <input type="text" name="isbn_13" value="{{ old('isbn_13', $book->isbn_13) }}"
                       class="w-full px-3 py-2 rounded-lg border font-mono text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">ISBN-10</label>
                <input type="text" name="isbn_10" value="{{ old('isbn_10', $book->isbn_10) }}"
                       class="w-full px-3 py-2 rounded-lg border font-mono text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            </div>
        </div>

        <!-- Two-column: Amazon and Lulu links -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Amazon Link</label>
                <input type="url" name="amazon_link" value="{{ old('amazon_link', $book->amazon_link) }}"
                       class="w-full px-3 py-2 rounded-lg border text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Lulu Link</label>
                <input type="url" name="lulu_link" value="{{ old('lulu_link', $book->lulu_link) }}"
                       class="w-full px-3 py-2 rounded-lg border text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            </div>
        </div>

        <!-- Three-column: image filenames -->
        <div class="grid grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Image (Front)</label>
                <input type="text" name="image_front" value="{{ old('image_front', $book->image_front) }}"
                       class="w-full px-3 py-2 rounded-lg border text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Image (Back)</label>
                <input type="text" name="image_back" value="{{ old('image_back', $book->image_back) }}"
                       class="w-full px-3 py-2 rounded-lg border text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Image (Inner)</label>
                <input type="text" name="image_inner" value="{{ old('image_inner', $book->image_inner) }}"
                       class="w-full px-3 py-2 rounded-lg border text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            </div>
        </div>

        <!-- Two-column: Edition and Published At -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Edition</label>
                <input type="text" name="edition" value="{{ old('edition', $book->edition) }}"
                       class="w-full px-3 py-2 rounded-lg border"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Published Date</label>
                <input type="date" name="published_at" value="{{ old('published_at', $book->published_at?->format('Y-m-d')) }}"
                       class="w-full px-3 py-2 rounded-lg border"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            </div>
        </div>

        <!-- Three-column: Page Count, Language, Priority -->
        <div class="grid grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Page Count</label>
                <input type="number" name="page_count" value="{{ old('page_count', $book->page_count) }}"
                       class="w-full px-3 py-2 rounded-lg border"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Language</label>
                <input type="text" name="language" value="{{ old('language', $book->language ?? 'English') }}"
                       class="w-full px-3 py-2 rounded-lg border"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Priority <span class="text-xs" style="color: var(--color-text-faint);">(0 = first)</span></label>
                <input type="number" name="priority" value="{{ old('priority', $book->priority ?? 0) }}"
                       min="0" max="65535"
                       class="w-full px-3 py-2 rounded-lg border"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            </div>
        </div>

        <!-- Price -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Price (USD)</label>
            <input type="number" step="0.01" name="price_usd" value="{{ old('price_usd', $book->price_usd) }}"
                   class="w-full px-3 py-2 rounded-lg border"
                   style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
        </div>

        <!-- Checkboxes -->
        <div class="flex gap-6 mb-6">
            <label class="flex items-center gap-2 text-sm" style="color: var(--color-text);">
                <input type="checkbox" name="published" value="1" {{ old('published', $book->published) ? 'checked' : '' }}>
                Published
            </label>
            <label class="flex items-center gap-2 text-sm" style="color: var(--color-text);">
                <input type="checkbox" name="active" value="1" {{ old('active', $book->active ?? true) ? 'checked' : '' }}>
                Active
            </label>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-4">
            <button type="submit"
                    class="px-6 py-2 text-sm rounded-lg font-medium"
                    style="background-color: var(--color-accent); color: var(--color-text-inv);">
                {{ $book->exists ? 'Update Book' : 'Create Book' }}
            </button>
            <a href="{{ route('admin.books.index') }}"
               class="text-sm"
               style="color: var(--color-text-muted);">Cancel</a>
        </div>
    </form>
</div>
@endsection

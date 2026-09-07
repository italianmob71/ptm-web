@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8">
    <h1 class="font-serif text-3xl font-bold mb-6" style="color: var(--color-text);">
        {{ $event->exists ? 'Edit: ' . $event->title : 'Add New Event' }}
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

    <form method="POST" action="{{ $event->exists ? route('admin.events.update', $event) : route('admin.events.store') }}">
        @csrf
        @if ($event->exists)
            @method('PUT')
        @endif

        <!-- Title -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Title *</label>
            <input type="text" name="title" value="{{ old('title', $event->title) }}"
                   class="w-full px-3 py-2 rounded-lg border"
                   style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                   required>
        </div>

        <!-- Description -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Description</label>
            <textarea name="description" rows="5"
                      class="w-full px-3 py-2 rounded-lg border"
                      style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">{{ old('description', $event->description) }}</textarea>
        </div>

        <!-- Two-column: Start/End -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Starts At *</label>
                <input type="datetime-local" name="starts_at"
                       value="{{ old('starts_at', $event->starts_at?->format('Y-m-d\TH:i')) }}"
                       class="w-full px-3 py-2 rounded-lg border font-mono text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Ends At <span class="text-xs" style="color: var(--color-text-faint);">(optional)</span></label>
                <input type="datetime-local" name="ends_at"
                       value="{{ old('ends_at', $event->ends_at?->format('Y-m-d\TH:i')) }}"
                       class="w-full px-3 py-2 rounded-lg border font-mono text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            </div>
        </div>

        <!-- Two-column: Location + Color -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Location</label>
                <input type="text" name="location" value="{{ old('location', $event->location) }}"
                       class="w-full px-3 py-2 rounded-lg border"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Color</label>
                <input type="color" name="color" value="{{ old('color', $event->color ?? '#f59e0b') }}"
                       class="w-full h-10 px-1 py-0.5 rounded-lg border cursor-pointer"
                       style="border-color: var(--color-border); background-color: var(--color-surface);">
            </div>
        </div>

        <!-- Checkboxes -->
        <div class="flex gap-6 mb-6">
            <label class="flex items-center gap-2 text-sm" style="color: var(--color-text);">
                <input type="checkbox" name="all_day" value="1" {{ old('all_day', $event->all_day) ? 'checked' : '' }}>
                All Day
            </label>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-4">
            <button type="submit"
                    class="px-6 py-2 text-sm rounded-lg font-medium"
                    style="background-color: var(--color-accent); color: var(--color-text-inv);">
                {{ $event->exists ? 'Update Event' : 'Create Event' }}
            </button>
            <a href="{{ route('admin.events.index') }}"
               class="text-sm"
               style="color: var(--color-text-muted);">Cancel</a>
        </div>
    </form>
</div>
@endsection

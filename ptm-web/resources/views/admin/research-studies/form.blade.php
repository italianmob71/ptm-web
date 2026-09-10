@extends('layouts.app')

@section('content')
<div class="content-admin mx-auto max-w-4xl px-4 py-8" style="color: var(--color-text);">
    <h1 class="font-serif text-3xl font-bold mb-6">{{ $study->exists ? 'Edit Research Study' : 'Add Research Study' }}</h1>
    @include('admin.research-studies.errors')
    <form method="POST" enctype="multipart/form-data" action="{{ $study->exists ? route('admin.research-studies.update', $study) : route('admin.research-studies.store') }}">
        @csrf
        @if ($study->exists)
            @method('PUT')
        @endif
        <div class="mb-4">
            <label for="study-title">Title *</label>
            <input id="study-title" name="title" value="{{ old('title', $study->title) }}" maxlength="255" required>
        </div>
        <div class="mb-4">
            <label for="study-description">Description *</label>
            <textarea id="study-description" name="description" data-content-editor rows="8">{{ old('description', $study->description) }}</textarea>
        </div>
        <p class="text-sm mb-4">Enter dates and times in {{ config('app.timezone') }}.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="starts-at">Start date and time *</label>
                <input id="starts-at" type="datetime-local" name="starts_at" value="{{ old('starts_at', $study->starts_at?->format('Y-m-d\TH:i')) }}" required>
            </div>
            <div>
                <label for="ends-at">End date and time *</label>
                <input id="ends-at" type="datetime-local" name="ends_at" value="{{ old('ends_at', $study->ends_at?->format('Y-m-d\TH:i')) }}" required>
            </div>
        </div>
        <div class="mb-4">
            <label for="application-url">Application link URL *</label>
            <input id="application-url" type="url" name="application_url" value="{{ old('application_url', $study->application_url) }}" maxlength="2048" required>
        </div>
        <div class="mb-4">
            <label for="application-cutoff">Application cutoff date and time</label>
            <input id="application-cutoff" type="datetime-local" name="application_cutoff_at" value="{{ old('application_cutoff_at', $study->application_cutoff_at?->format('Y-m-d\TH:i')) }}">
            <p class="text-sm mt-1">Defaults to 24 hours before the start. You may choose any date, or leave blank to use the default. Applications always close within 24 hours of the start.</p>
        </div>
        <div class="mb-6">
            <label for="study-image">Study image (500 × 500 pixels) *</label>
            @if ($study->image_path)
                <img src="{{ asset($study->image_path) }}" alt="Current study image" width="150" height="150" class="mb-2">
                <p class="text-sm mb-2">The current image will be retained unless you upload a replacement.</p>
            @endif
            <input id="study-image" type="file" name="image" accept="image/jpeg,image/png,image/webp" @required(!$study->image_path)>
            <p class="text-sm mt-1">JPEG, PNG, or WebP, exactly 500 × 500 pixels, up to 10 MB.</p>
        </div>
        <button type="submit" class="px-6 py-2 rounded-lg" style="background: var(--color-accent); color: var(--color-text-inv);">{{ $study->exists ? 'Update Research Study' : 'Create Research Study' }}</button>
        <a href="{{ route('admin.research-studies.index') }}" class="ml-4">Cancel</a>
    </form>
</div>
@include('admin.research-studies.editor')
<script>
(function () {
    const start = document.getElementById('starts-at');
    const cutoff = document.getElementById('application-cutoff');
    let automatic = !cutoff.value;
    cutoff.addEventListener('input', () => { automatic = !cutoff.value; });
    function updateCutoff() {
        if (!automatic || !start.value) return;
        // Treat the form values as wall-clock times in the displayed application timezone.
        const date = new Date(start.value + 'Z');
        if (Number.isNaN(date.getTime())) return;
        date.setUTCHours(date.getUTCHours() - 24);
        cutoff.value = date.toISOString().slice(0, 16);
    }
    start.addEventListener('input', updateCutoff);
    updateCutoff();
})();
</script>
@endsection

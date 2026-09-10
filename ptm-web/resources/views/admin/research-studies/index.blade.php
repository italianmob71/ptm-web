@extends('layouts.app')

@section('content')
<div class="content-admin mx-auto max-w-6xl px-4 py-8" style="color: var(--color-text);">
    <h1 class="font-serif text-3xl font-bold mb-6">Research Studies</h1>
    @if (session('status'))
        <p role="status" class="mb-4" style="color: var(--color-success);">{{ session('status') }}</p>
    @endif
    @include('admin.research-studies.errors')

    <section class="mb-12">
        <h2 class="font-serif text-2xl mb-4">Research page content</h2>
        <form method="POST" action="{{ route('admin.research-studies.page.update') }}">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="page-title">Page heading *</label>
                <input id="page-title" name="title" value="{{ old('title', $page->title) }}" maxlength="255" required>
            </div>
            <div class="mb-4">
                <label for="page-content">Introduction *</label>
                <textarea id="page-content" name="content" data-content-editor rows="8">{{ old('content', $page->content) }}</textarea>
            </div>
            <button type="submit" class="px-6 py-2 rounded-lg" style="background: var(--color-accent); color: var(--color-text-inv);">Save page content</button>
            <a href="{{ route('get-involved') }}" class="ml-4 underline">View page</a>
        </form>
    </section>

    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
        <h2 class="font-serif text-2xl">All research studies</h2>
        <a href="{{ route('admin.research-studies.create') }}" class="px-4 py-2 rounded-lg" style="background: var(--color-accent); color: var(--color-text-inv);">+ Add Research Study</a>
    </div>
    <p class="text-sm mb-4">Dates are shown in {{ config('app.timezone') }}. Past studies remain here for editing.</p>
    <div class="overflow-x-auto rounded-lg border" style="border-color: var(--color-border);">
        <table class="min-w-full text-sm text-left">
            <thead style="background: var(--color-surface-2);">
                <tr>
                    <th class="px-4 py-3">Image</th>
                    <th class="px-4 py-3">Title</th>
                    <th class="px-4 py-3">Starts / Ends</th>
                    <th class="px-4 py-3">Application cutoff</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($studies as $study)
                    <tr style="border-top: 1px solid var(--color-border);">
                        <td class="px-4 py-3"><img src="{{ asset($study->image_path) }}" alt="" width="64" height="64" style="width:64px; height:64px; object-fit:cover;"></td>
                        <td class="px-4 py-3">{{ $study->title }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $study->starts_at->format('M j, Y g:i a') }}<br>{{ $study->ends_at->format('M j, Y g:i a') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $study->application_cutoff_at->format('M j, Y g:i a') }}</td>
                        <td class="px-4 py-3">{{ $study->ends_at->isPast() ? 'Past' : ($study->starts_at->isFuture() ? 'Upcoming' : 'Ongoing') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <a href="{{ route('admin.research-studies.edit', $study) }}" class="underline mr-3">Edit</a>
                            <form method="POST" action="{{ route('admin.research-studies.destroy', $study) }}" class="inline-block" onsubmit="return confirm('Delete this research study and its image?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="color: var(--color-danger);">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center">No research studies yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $studies->links() }}</div>
</div>
@include('admin.research-studies.editor')
@endsection

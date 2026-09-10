@extends('layouts.app')

@section('content')
<div class="content-admin mx-auto max-w-6xl px-4 py-8" style="color:var(--color-text);">
    <h1 class="font-serif text-3xl font-bold mb-6">Special Studies</h1>
    @if (session('status'))
        <p role="status" class="mb-4" style="color:var(--color-success);">{{ session('status') }}</p>
    @endif
    @include('admin.research-studies.errors')
    <section class="mb-12">
        <h2 class="font-serif text-2xl mb-4">Page content</h2>
        <form method="POST" action="{{ route('admin.special-studies.page.update') }}">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="page-title">Page heading *</label>
                <input id="page-title" name="title" value="{{ old('title', $page->title) }}" maxlength="255" required>
            </div>
            <div class="mb-4">
                <label for="page-description">Description *</label>
                <textarea id="page-description" name="description" rows="8" data-content-editor>{{ old('description', $page->description) }}</textarea>
            </div>
            <button type="submit" class="px-6 py-2 rounded-lg" style="background:var(--color-accent); color:var(--color-text-inv);">Save page content</button>
            <a href="{{ route('special-studies') }}" class="ml-4 underline">View page</a>
        </form>
    </section>
    <section>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
            <h2 class="font-serif text-2xl">Special Studies for Download</h2>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('admin.pdfs.create', ['from' => 'special-studies']) }}" class="px-4 py-2 rounded-lg" style="background:var(--color-accent); color:var(--color-text-inv);">Upload PDF</a>
                <a href="{{ route('admin.pdfs.index') }}" class="px-4 py-2 underline">Manage PDFs</a>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.special-studies.store') }}" class="mb-6">
            @csrf
            <label for="pdf-id">Choose a PDF from the library</label>
            <div class="flex flex-wrap gap-3">
                <select id="pdf-id" name="pdf_id" required class="flex-1 min-w-0 px-3 py-2 rounded-lg border" style="background:var(--color-surface); color:var(--color-text); border-color:var(--color-border);">
                    <option value="">Select a PDF…</option>
                    @foreach ($pdfs as $pdf)
                        <option value="{{ $pdf->id }}" @selected(old('pdf_id', session('uploaded_pdf_id')) == $pdf->id)>{{ $pdf->title ?: $pdf->filename }}{{ $pdf->category ? ' — '.$pdf->category : '' }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 rounded-lg border" style="border-color:var(--color-border);">Add to list</button>
            </div>
        </form>
        <p class="text-sm mb-4">Use Up and Down to arrange downloads. Titles come from the PDF library. Removing a row keeps the PDF in the library.</p>
        <div class="overflow-x-auto rounded-lg border" style="border-color:var(--color-border);">
            <table class="min-w-full text-sm text-left">
                <thead style="background:var(--color-surface-2);"><tr><th class="px-4 py-3">PDF</th><th class="px-4 py-3">Title</th><th class="px-4 py-3">Order</th><th class="px-4 py-3">Actions</th></tr></thead>
                <tbody>
                    @forelse ($studies as $study)
                        <tr style="border-top:1px solid var(--color-border);">
                            <td class="px-4 py-3"><x-pdf-icon /></td>
                            <td class="px-4 py-3">
                                @if ($study->pdf)
                                    <a href="{{ $study->pdf->url }}" class="underline">{{ $study->pdf->title ?: $study->pdf->filename }}</a>
                                @else
                                    PDF removed from the library (hidden on public page)
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @foreach (['up' => 'Up', 'down' => 'Down'] as $direction => $label)
                                    <form method="POST" action="{{ route('admin.special-studies.move', $study) }}" class="inline-block">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="direction" value="{{ $direction }}">
                                        <button type="submit" class="px-2 py-1 border rounded disabled:opacity-40" @disabled(($direction === 'up' && $loop->parent->first) || ($direction === 'down' && $loop->parent->last)) aria-label="Move {{ $study->pdf?->title ?: $study->pdf?->filename ?: 'PDF' }} {{ $direction }}">{{ $label }}</button>
                                    </form>
                                @endforeach
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if ($study->pdf)
                                    <a href="{{ route('admin.pdfs.edit', $study->pdf) }}" class="underline mr-3">Edit PDF</a>
                                @endif
                                <form method="POST" action="{{ route('admin.special-studies.destroy', $study) }}" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="color:var(--color-danger);">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center">No PDFs selected yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@include('admin.partials.content-editor')
@endsection

@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-serif text-3xl font-bold" style="color: var(--color-text);">
            Events Dashboard
            @if ($showAll)
                <span class="text-sm font-normal ml-2" style="color: var(--color-text-muted);">(showing all)</span>
            @else
                <span class="text-sm font-normal ml-2" style="color: var(--color-text-muted);">(current + upcoming only)</span>
            @endif
        </h1>
        <div class="flex items-center gap-3">
            @if ($showAll)
                <a href="{{ route('admin.events.index') }}"
                   class="px-4 py-2 text-sm rounded-lg border"
                   style="border-color: var(--color-border); color: var(--color-text);">Current + Upcoming</a>
            @else
                <a href="{{ route('admin.events.index', ['all' => 1]) }}"
                   class="px-4 py-2 text-sm rounded-lg border"
                   style="border-color: var(--color-border); color: var(--color-text);">Show All (incl. past)</a>
            @endif
            <a href="{{ route('admin.events.create') }}"
               class="px-4 py-2 text-sm rounded-lg font-medium"
               style="background-color: var(--color-accent); color: var(--color-text-inv);">
                + Add New Event
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg" style="background-color: var(--color-surface-2); border: 1px solid var(--color-border); color: var(--color-success);">
            {{ session('status') }}
        </div>
    @endif

    <div class="overflow-x-auto rounded-lg border" style="border-color: var(--color-border); background-color: var(--color-surface);">
        <table class="min-w-full text-sm">
            <thead style="background-color: var(--color-surface-2);">
                <tr>
                    <th class="text-left px-4 py-3 font-medium" style="color: var(--color-text-muted);">Starts</th>
                    <th class="text-left px-4 py-3 font-medium" style="color: var(--color-text-muted);">Ends</th>
                    <th class="text-left px-4 py-3 font-medium" style="color: var(--color-text-muted);">Title</th>
                    <th class="text-left px-4 py-3 font-medium" style="color: var(--color-text-muted);">Location</th>
                    <th class="text-left px-4 py-3 font-medium" style="color: var(--color-text-muted);">All Day</th>
                    <th class="text-left px-4 py-3 font-medium" style="color: var(--color-text-muted);">Status</th>
                    <th class="text-right px-4 py-3 font-medium" style="color: var(--color-text-muted);">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($events as $event)
                    @php
                        $now = now();
                        $isPast = $event->starts_at->lt($now) && (!$event->ends_at || $event->ends_at->lt($now));
                        $isLive = $event->starts_at->lte($now) && $event->ends_at && $event->ends_at->gte($now);
                    @endphp
                    <tr style="border-top: 1px solid var(--color-border-soft);">
                        <td class="px-4 py-3 font-mono text-xs whitespace-nowrap" style="color: var(--color-text);">
                            {{ $event->starts_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-4 py-3 font-mono text-xs whitespace-nowrap" style="color: var(--color-text-muted);">
                            {{ $event->ends_at?->format('Y-m-d H:i') ?? '—' }}
                        </td>
                        <td class="px-4 py-3" style="color: var(--color-text);">
                            <span class="inline-block w-3 h-3 rounded-full mr-2 align-middle" style="background-color: {{ $event->color }};"></span>
                            {{ $event->title }}
                        </td>
                        <td class="px-4 py-3" style="color: var(--color-text-muted);">
                            {{ $event->location ?? '—' }}
                        </td>
                        <td class="px-4 py-3" style="color: var(--color-text-muted);">
                            {{ $event->all_day ? 'Yes' : '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @if ($isLive)
                                <span class="px-2 py-0.5 rounded text-xs font-medium"
                                      style="background-color: var(--color-success); color: var(--color-text-inv);">Live</span>
                            @elseif ($isPast)
                                <span class="px-2 py-0.5 rounded text-xs font-medium"
                                      style="background-color: var(--color-surface-3); color: var(--color-text-muted);">Past</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs font-medium"
                                      style="background-color: var(--color-accent); color: var(--color-text-inv);">Upcoming</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.events.edit', $event) }}"
                               class="inline-block px-3 py-1 text-xs rounded border"
                               style="border-color: var(--color-border); color: var(--color-text);">Edit</a>
                            <form method="POST" action="{{ route('admin.events.destroy', $event) }}"
                                  class="inline-block"
                                  onsubmit="return confirm('Delete this event? This is a soft delete.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1 text-xs rounded border"
                                        style="border-color: var(--color-danger); color: var(--color-danger);">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center" style="color: var(--color-text-muted);">
                            No events{{ $showAll ? '' : ' (current or upcoming)' }} yet. Click "Add New Event" to create one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $events->links() }}
</div>
@endsection

@extends('layouts.app')

@section('content')
<!-- Events Calendar Page -->
<div class="mx-auto max-w-7xl px-4 py-8" x-data="eventsCalendar()">
    <!-- Calendar Header with Navigation & View Selectors -->
    <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <!-- Prev / Next Pagination (Left) -->
        <div class="flex items-center gap-2">
            <a href="{{ route('events', array_merge(request()->query(), ['date' => $prevDate])) }}"
               class="btn-icon" aria-label="Previous {{ $view }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <a href="{{ route('events', array_merge(request()->query(), ['date' => $todayDate])) }}"
               class="btn-icon" aria-label="Today">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </a>
            <a href="{{ route('events', array_merge(request()->query(), ['date' => $nextDate])) }}"
               class="btn-icon" aria-label="Next {{ $view }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <!-- Centered Month/Year Title -->
        <div class="text-center">
            <h1 class="text-2xl md:text-3xl font-serif font-semibold" style="color: var(--color-text);">
                {{ $calendar['month'] ?? $calendar['label'] ?? $baseDate->format('F Y') }}
            </h1>
            <p class="text-sm" style="color: var(--color-text-muted);">
                {{ $view === 'day' ? 'Day View' : ($view === 'week' ? 'Week View' : 'Month View') }}
            </p>
        </div>

        <!-- View Selectors (Right) -->
        <div class="flex items-center gap-2" role="group" aria-label="Calendar view">
            @foreach(['month' => 'Month', 'week' => 'Week', 'day' => 'Day'] as $key => $label)
                <a href="{{ route('events', array_merge(request()->query(), ['view' => $key])) }}"
                   class="px-3 py-1.5 text-sm rounded-md transition"
                   style="color: {{ $view === $key ? 'var(--color-text-inv)' : 'var(--color-text-muted)' }};
                          background-color: {{ $view === $key ? 'var(--color-accent)' : 'transparent' }};">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </header>

    <!-- Calendar Grid -->
    <main>
        @if($view === 'month')
            <!-- Month Grid -->
            <div class="grid grid-cols-7 gap-0 rounded-lg" style="border: 1px solid var(--color-border);">
                <!-- Day Headers -->
                @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                    <div class="p-3 text-center text-xs font-semibold uppercase" style="background-color: var(--color-surface); border-bottom: 1px solid var(--color-border); color: var(--color-text-muted);">
                        {{ $day }}
                    </div>
                @endforeach

                <!-- Weeks -->
                @foreach($calendar['weeks'] as $weekIndex => $week)
                    @foreach($week as $day)
                        <div class="relative min-h-[100px] p-2" style="border-right: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border); background-color: {{ $day['is_current_month'] ? 'var(--color-bg)' : 'var(--color-surface)' }}; {{ !$day['is_current_month'] ? 'opacity: 0.6;' : '' }} {{ $day['is_today'] ? 'outline: 2px solid var(--color-accent); outline-offset: -2px;' : '' }}">
                            <span class="text-sm font-medium" style="color: {{ $day['is_today'] ? 'var(--color-accent)' : 'var(--color-text)' }};">{{ $day['day'] }}</span>

                            @foreach($day['events']->take(3) as $event)
                                <button type="button"
                                        class="event-chip mt-1.5 px-2 py-1 text-xs rounded truncate"
                                        style="background-color: {{ $event['color'] }}; color: white; border: 0; text-align: left; width: 100%; cursor: pointer;"
                                        @click="openEvent({{ $weekIndex }}, {{ $loop->parent->index }}, {{ $loop->index }})">
                                    {{ $event['title'] }}
                                </button>
                            @endforeach

                            @if($day['events']->count() > 3)
                                <div class="mt-1 text-xs text-center" style="color: var(--color-text-muted);">
                                    +{{ $day['events']->count() - 3 }} more
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endforeach
            </div>

        @elseif($view === 'week')
            <!-- Week View -->
            <div class="grid grid-cols-7 gap-0 rounded-lg" style="border: 1px solid var(--color-border);">
                @foreach($calendar['days'] as $day)
                    <div class="relative min-h-[200px] p-2" style="border-right: 1px solid var(--color-border); background-color: {{ $day['is_today'] ? 'var(--color-accent)' : 'var(--color-bg)' }}; opacity: {{ $day['is_today'] ? 0.1 : 1 }};">
                        <div class="mb-2">
                            <span class="text-sm font-medium uppercase" style="color: var(--color-text-muted);">{{ $day['label'] }}</span>
                            <div class="text-lg font-semibold" style="color: {{ $day['is_today'] ? 'var(--color-accent)' : 'var(--color-text)' }};">{{ $day['date'] }}</div>
                        </div>
                        <div class="space-y-1">
                            @foreach($day['events'] as $event)
                                <button type="button"
                                        class="event-chip w-full px-2 py-1 text-xs rounded text-left"
                                        style="background-color: {{ $event['color'] }}; color: white; border: 0; cursor: pointer;"
                                        @click="openEvent(0, {{ $loop->parent->index }}, {{ $loop->index }})">
                                    {{ $event['title'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

        @elseif($view === 'day')
            <!-- Day View (event list, click to open modal) -->
            <div class="rounded-lg p-4" style="border: 1px solid var(--color-border); background-color: var(--color-surface);">
                <h2 class="text-xl font-serif font-semibold mb-4" style="color: var(--color-text);">{{ $calendar['label'] }}</h2>
                @php $hourEvents = collect($calendar['events'] ?? []); @endphp
                @if($hourEvents->isEmpty())
                    <p class="text-sm" style="color: var(--color-text-muted);">No events on this day.</p>
                @else
                    <div class="space-y-2">
                        @foreach($hourEvents as $event)
                            <button type="button"
                                    class="event-chip w-full px-3 py-2 text-sm rounded text-left"
                                    style="background-color: var(--color-surface-2); border-left: 4px solid {{ $event['color'] }}; color: var(--color-text); cursor: pointer;"
                                    @click="openEvent(0, 0, {{ $loop->index }})">
                                <div class="font-semibold">{{ $event['title'] }}</div>
                                <div class="text-xs" style="color: var(--color-text-muted);">
                                    {{ \Illuminate\Support\Carbon::parse($event['starts_at'])->format('g:i A') }}
                                    @if($event['ends_at']) – {{ \Illuminate\Support\Carbon::parse($event['ends_at'])->format('g:i A') }}@endif
                                    @if($event['location']) · {{ $event['location'] }}@endif
                                </div>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </main>

    <p class="mt-6 text-sm text-center italic" style="color: var(--color-text-muted);">
        🗓️ Click any event to see full details.
    </p>

    <!-- Event Details Modal -->
    <div
        x-show="modal.open"
        x-transition.opacity
        class="event-modal-backdrop"
        @click.self="closeEvent()"
        @keydown.escape.window="closeEvent()"
        style="display: none;"
    >
        <div
            class="event-modal"
            role="dialog"
            aria-modal="true"
            :aria-labelledby="'event-modal-title'"
            @click.stop
        >
            <button
                type="button"
                class="event-modal-close"
                @click="closeEvent()"
                aria-label="Close"
            >&times;</button>

            <div class="event-modal-accent" :style="modal.event && modal.event.color ? `background-color: ${modal.event.color}` : ''"></div>

            <div class="event-modal-body">
                <h2 id="event-modal-title" class="font-serif text-2xl font-bold mb-2" x-text="modal.event?.title"></h2>

                <div class="event-modal-meta" x-show="modal.event">
                    <div>
                        <svg class="inline w-4 h-4 align-text-bottom" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span x-text="formatDate(modal.event?.starts_at)"></span>
                        <template x-if="modal.event?.ends_at">
                            <span> &ndash; <span x-text="formatTime(modal.event.ends_at)"></span></span>
                        </template>
                    </div>
                    <div x-show="modal.event?.location">
                        <svg class="inline w-4 h-4 align-text-bottom" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span x-text="modal.event?.location"></span>
                    </div>
                </div>

                <div class="event-modal-desc" x-show="modal.event?.description" x-html="modal.event?.description"></div>

            <div class="event-modal-desc" x-show="!modal.event?.description" style="font-style: italic; opacity: 0.7;">
                No description provided.
            </div>

            <div class="mt-6 text-right">
                <button type="button" class="event-modal-btn" @click="closeEvent()">Close</button>
            </div>
            </div>
        </div>
    </div>
</div>

<script>
function eventsCalendar() {
    return {
        modal: { open: false, event: null },
        calendar: @js($calendar),
        view: "{{ $view }}",
        openEvent(weekIndex, dayIndex, eventIndex) {
            let event = null;
            if (this.view === 'month') {
                const weeks = this.calendar.weeks || [];
                const week = weeks[weekIndex];
                const day = week?.[dayIndex];
                event = day?.events?.[eventIndex] || null;
            } else if (this.view === 'week') {
                const days = this.calendar.days || [];
                const day = days[dayIndex];
                event = day?.events?.[eventIndex] || null;
            } else if (this.view === 'day') {
                const events = this.calendar.events || [];
                event = events[eventIndex] || null;
            }
            this.modal.event = event;
            this.modal.open = true;
            document.body.style.overflow = 'hidden';
        },
        closeEvent() {
            this.modal.open = false;
            this.modal.event = null;
            document.body.style.overflow = '';
        },
        formatDate(iso) {
            if (!iso) return '';
            const d = new Date(iso);
            if (isNaN(d.getTime())) return iso;
            return d.toLocaleDateString(undefined, { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' })
                 + ' \u00b7 '
                 + d.toLocaleTimeString(undefined, { hour: 'numeric', minute: '2-digit' });
        },
        formatTime(iso) {
            if (!iso) return '';
            const d = new Date(iso);
            return d.toLocaleTimeString(undefined, { hour: 'numeric', minute: '2-digit' });
        }
    }
}
</script>

<style>
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: var(--radius-md);
        transition: background-color 0.2s ease, color 0.2s ease;
        color: var(--color-text-muted);
        background-color: var(--color-surface);
        border: 1px solid var(--color-border);
    }
    .btn-icon:hover {
        background-color: var(--color-accent);
        color: var(--color-text-inv);
        border-color: var(--color-accent);
    }

    .event-chip { transition: filter 0.15s ease, opacity 0.15s ease; }
    .event-chip:hover { filter: brightness(1.1); }

    /* Modal */
    .event-modal-backdrop {
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.55);
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .event-modal {
        position: relative;
        width: 100%;
        max-width: 520px;
        max-height: 90vh;
        overflow-y: auto;
        background-color: var(--color-surface);
        color: var(--color-text);
        border: 1px solid var(--color-border);
        border-radius: 0.75rem;
        box-shadow: 0 20px 50px rgba(0,0,0,0.4);
    }
    .event-modal-accent {
        height: 8px;
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
        background-color: var(--color-accent);
    }
    .event-modal-body { padding: 1.25rem 1.5rem 1.5rem; }
    .event-modal-close {
        position: absolute;
        top: 0.5rem;
        right: 0.75rem;
        background: transparent;
        border: 0;
        font-size: 1.75rem;
        line-height: 1;
        cursor: pointer;
        color: var(--color-text-muted);
        z-index: 2;
    }
    .event-modal-close:hover { color: var(--color-text); }
    .event-modal-meta {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        margin-bottom: 1rem;
        padding: 0.75rem 0;
        border-top: 1px solid var(--color-border-soft, var(--color-border));
        border-bottom: 1px solid var(--color-border-soft, var(--color-border));
        color: var(--color-text-muted);
        font-size: 0.875rem;
    }
    .event-modal-desc {
        margin-top: 0.5rem;
        line-height: 1.6;
        white-space: pre-wrap;
        color: var(--color-text);
    }
    .event-modal-btn {
        padding: 0.5rem 1.25rem;
        font-size: 0.875rem;
        font-weight: 500;
        background-color: var(--color-accent);
        color: var(--color-text-inv);
        border: 0;
        border-radius: 0.5rem;
        cursor: pointer;
    }
    .event-modal-btn:hover { filter: brightness(0.9); }
</style>
@endsection

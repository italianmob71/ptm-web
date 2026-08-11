@extends('layouts.app')

@section('content')
<!-- Events Calendar Page -->
<div class="mx-auto max-w-7xl px-4 py-8">
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
            <div class="grid grid-cols-7 gap-0" style="border: 1px solid var(--color-border); border-radius: var(--radius-lg); overflow: hidden;">
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
                                <div class="mt-1.5 px-2 py-1 text-xs rounded truncate" style="background-color: {{ $event['color'] }}; color: white;">
                                    {{ $event['title'] }}
                                </div>
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
            <div class="grid grid-cols-7 gap-0" style="border: 1px solid var(--color-border); border-radius: var(--radius-lg); overflow: hidden;">
                @foreach($calendar['days'] as $day)
                    <div class="relative min-h-[200px] p-2" style="border-right: 1px solid var(--color-border); background-color: {{ $day['is_today'] ? 'var(--color-accent)' : 'var(--color-bg)' }}; opacity: {{ $day['is_today'] ? 0.1 : 1 }};">
                        <div class="mb-2">
                            <span class="text-sm font-medium uppercase" style="color: var(--color-text-muted);">{{ $day['label'] }}</span>
                            <div class="text-lg font-semibold" style="color: {{ $day['is_today'] ? 'var(--color-accent)' : 'var(--color-text)' }};">{{ $day['date'] }}</div>
                        </div>
                        <div class="space-y-1">
                            @foreach($day['events'] as $event)
                                <div class="px-2 py-1 text-xs rounded" style="background-color: {{ $event['color'] }}; color: white;">
                                    {{ $event['title'] }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

        @elseif($view === 'day')
            <!-- Day View -->
            <div class="grid grid-cols-1 gap-0" style="border: 1px solid var(--color-border); border-radius: var(--radius-lg); overflow: hidden;">
                <div class="p-4 border-b" style="border-color: var(--color-border); background-color: var(--color-surface);">
                    <h2 class="text-xl font-serif font-semibold" style="color: var(--color-text);">{{ $calendar['label'] }}</h2>
                </div>
                <div class="grid grid-cols-24 gap-0">
                    @foreach($calendar['hours'] as $hour)
                        <div class="relative h-12 border-r" style="border-color: var(--color-border);">
                            <span class="text-xs text-right pr-2" style="color: var(--color-text-muted);">{{ sprintf('%02d:00', $hour) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </main>
</div>

<style>
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: var(--radius-md);
        transition: background-color 0.2s ease, color 0.2s ease;
        style="color: var(--color-text-muted); background-color: var(--color-surface); border: 1px solid var(--color-border);"
    }
    .btn-icon:hover {
        style="background-color: var(--color-accent); color: var(--color-text-inv); border-color: var(--color-accent);"
    }
</style>
@endsection
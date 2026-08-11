<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventCalendarController extends Controller
{
    public function index(Request $request)
    {
        // View mode: month, week, day (default: month)
        $view = $request->query('view', 'month');
        
        // Base date for navigation
        $baseDate = $request->query('date') 
            ? Carbon::parse($request->query('date')) 
            : now();
        
        // Ensure base date is valid
        if (!$baseDate->isValid()) {
            $baseDate = now();
        }

        // Calculate the date range based on view
        $period = $this->getDateRange($baseDate, $view);
        
        // Fetch events for the period
        $events = Event::inRange($period['start'], $period['end'])
            ->orderBy('starts_at')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'starts_at' => $event->starts_at->toISOString(),
                    'ends_at' => $event->ends_at?->toISOString(),
                    'all_day' => $event->all_day,
                    'location' => $event->location,
                    'color' => $event->color,
                ];
            });

        // Generate calendar grid data
        $calendar = $this->generateCalendar($baseDate, $view, $events);

        // Navigation dates
        $prevDate = $this->getPrevDate($baseDate, $view);
        $nextDate = $this->getNextDate($baseDate, $view);
        $todayDate = now()->format('Y-m-d');

        return view('events.index', [
            'title' => 'Events Calendar',
            'view' => $view,
            'baseDate' => $baseDate,
            'period' => $period,
            'calendar' => $calendar,
            'events' => $events,
            'prevDate' => $prevDate,
            'nextDate' => $nextDate,
            'todayDate' => $todayDate,
        ]);
    }

    private function getDateRange(Carbon $baseDate, string $view): array
    {
        return match ($view) {
            'day' => [
                'start' => $baseDate->copy()->startOfDay(),
                'end' => $baseDate->copy()->endOfDay(),
            ],
            'week' => [
                'start' => $baseDate->copy()->startOfWeek(),
                'end' => $baseDate->copy()->endOfWeek(),
            ],
            'month' => [
                'start' => $baseDate->copy()->startOfMonth()->startOfWeek(),
                'end' => $baseDate->copy()->endOfMonth()->endOfWeek(),
            ],
            default => [
                'start' => $baseDate->copy()->startOfMonth()->startOfWeek(),
                'end' => $baseDate->copy()->endOfMonth()->endOfWeek(),
            ],
        };
    }

    private function getPrevDate(Carbon $baseDate, string $view): string
    {
        return match ($view) {
            'day' => $baseDate->copy()->subDay()->format('Y-m-d'),
            'week' => $baseDate->copy()->subWeek()->format('Y-m-d'),
            'month' => $baseDate->copy()->subMonth()->format('Y-m-d'),
            default => $baseDate->copy()->subMonth()->format('Y-m-d'),
        };
    }

    private function getNextDate(Carbon $baseDate, string $view): string
    {
        return match ($view) {
            'day' => $baseDate->copy()->addDay()->format('Y-m-d'),
            'week' => $baseDate->copy()->addWeek()->format('Y-m-d'),
            'month' => $baseDate->copy()->addMonth()->format('Y-m-d'),
            default => $baseDate->copy()->addMonth()->format('Y-m-d'),
        };
    }

    private function generateCalendar(Carbon $baseDate, string $view, $events): array
    {
        return match ($view) {
            'day' => $this->generateDayView($baseDate, $events),
            'week' => $this->generateWeekView($baseDate, $events),
            'month' => $this->generateMonthView($baseDate, $events),
            default => $this->generateMonthView($baseDate, $events),
        };
    }

    private function generateDayView(Carbon $date, $events): array
    {
        $dayEvents = $events->filter(function ($e) use ($date) {
            $start = Carbon::parse($e['starts_at']);
            $end = $e['ends_at'] ? Carbon::parse($e['ends_at']) : $start->copy()->addHour();
            return $date->between($start, $end) || $date->equalTo($start) || $date->equalTo($end);
        })->values();

        return [
            'date' => $date->format('Y-m-d'),
            'label' => $date->format('l, F j, Y'),
            'events' => $dayEvents,
            'hours' => range(0, 23),
        ];
    }

    private function generateWeekView(Carbon $baseDate, $events): array
    {
        $start = $baseDate->copy()->startOfWeek();
        $days = [];
        
        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i);
            $dayEvents = $events->filter(function ($e) use ($day) {
                $start = Carbon::parse($e['starts_at']);
                $end = $e['ends_at'] ? Carbon::parse($e['ends_at']) : $start->copy()->addHour();
                return $day->between($start, $end) || $day->equalTo($start) || $day->equalTo($end);
            })->values();

            $days[] = [
                'date' => $day->format('Y-m-d'),
                'label' => $day->format('D, M j'),
                'events' => $dayEvents,
                'is_today' => $day->isToday(),
            ];
        }

        return [
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $start->copy()->addDays(6)->format('Y-m-d'),
            'days' => $days,
        ];
    }

    private function generateMonthView(Carbon $baseDate, $events): array
    {
        $start = $baseDate->copy()->startOfMonth()->startOfWeek();
        $end = $baseDate->copy()->endOfMonth()->endOfWeek();
        $weeks = [];
        $week = [];

        $current = $start->copy();
        while ($current <= $end) {
            $dayEvents = $events->filter(function ($e) use ($current) {
                $start = Carbon::parse($e['starts_at']);
                $end = $e['ends_at'] ? Carbon::parse($e['ends_at']) : $start->copy()->addHour();
                return $current->between($start, $end) || $current->equalTo($start) || $current->equalTo($end);
            })->values();

            $week[] = [
                'date' => $current->format('Y-m-d'),
                'day' => $current->day,
                'is_current_month' => $current->month === $baseDate->month,
                'is_today' => $current->isToday(),
                'events' => $dayEvents,
            ];

            if ($current->dayOfWeek === Carbon::SATURDAY) {
                $weeks[] = $week;
                $week = [];
            }

            $current->addDay();
        }

        if (!empty($week)) {
            $weeks[] = $week;
        }

        return [
            'month' => $baseDate->format('F Y'),
            'month_num' => $baseDate->month,
            'year' => $baseDate->year,
            'weeks' => $weeks,
        ];
    }
}
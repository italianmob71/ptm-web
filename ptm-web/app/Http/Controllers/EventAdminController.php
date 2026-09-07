<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventAdminController extends Controller
{
    /**
     * List events. Default: in-progress or future. ?all=1 shows everything.
     */
    public function index(Request $request)
    {
        $showAll = $request->boolean('all');

        $query = Event::query();

        if (! $showAll) {
            // In-progress or upcoming: starts in the future, OR has ended_at >= now
            $query->where(function ($q) {
                $q->where('starts_at', '>=', now())
                  ->orWhere(function ($q2) {
                      $q2->whereNotNull('ends_at')
                         ->where('ends_at', '>=', now());
                  });
            });
        }

        $events = $query->orderBy('starts_at')->paginate(25)->withQueryString();

        return view('admin.events.index', [
            'title'   => 'Events Dashboard',
            'events'  => $events,
            'showAll' => $showAll,
        ]);
    }

    public function create()
    {
        return view('admin.events.form', [
            'title' => 'Add New Event',
            'event' => new Event(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateEvent($request);

        $event = Event::create($data + ['created_by' => auth()->id()]);

        return redirect()
            ->route('admin.events.index')
            ->with('status', "Event \"{$event->title}\" created.");
    }

    public function edit(Event $event)
    {
        return view('admin.events.form', [
            'title' => 'Edit Event',
            'event' => $event,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $data = $this->validateEvent($request);

        $event->update($data);

        return redirect()
            ->route('admin.events.index')
            ->with('status', "Event \"{$event->title}\" updated.");
    }

    public function destroy(Event $event)
    {
        $title = $event->title;
        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('status', "Event \"{$title}\" deleted.");
    }

    private function validateEvent(Request $request): array
    {
        return $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'starts_at'   => 'required|date',
            'ends_at'     => 'nullable|date|after_or_equal:starts_at',
            'all_day'     => 'nullable|boolean',
            'location'    => 'nullable|string|max:255',
            'color'       => 'nullable|string|size:7|regex:/^#[0-9a-fA-F]{6}$/',
        ]) + [
            'all_day' => $request->boolean('all_day'),
        ];
    }
}

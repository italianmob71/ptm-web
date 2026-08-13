<?php

namespace App\Http\Controllers;

use App\Models\TravelNote;
use Illuminate\Http\Request;

class TravelNoteController extends Controller
{
    /**
     * Travel Notes index — display all published notes, ordered by sort_order.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('q', ''));

        $query = TravelNote::with(['teaserImage', 'author'])->published()->ordered();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('biblical_reference', 'like', "%{$search}%");
            });
        }

        $notes = $query->paginate(12)->appends(['q' => $search]);

        return view('travel-notes.index', [
            'title' => "Bryan's Travel Notes",
            'notes' => $notes,
            'search' => $search,
        ]);
    }

    /**
     * Show a single travel note by slug.
     */
    public function show(string $slug)
    {
        $note = TravelNote::with(['teaserImage', 'author'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $related = TravelNote::with(['teaserImage', 'author'])
            ->published()
            ->where('id', '!=', $note->id)
            ->ordered()
            ->limit(3)
            ->get();

        return view('travel-notes.show', [
            'title' => $note->title,
            'note' => $note,
            'related' => $related,
        ]);
    }
}

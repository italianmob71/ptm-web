<?php

namespace App\Http\Controllers;

use App\Models\TravelNote;
use Illuminate\Http\Request;

class TravelNoteAdminController extends Controller
{
    /**
     * Travel Notes dashboard.
     */
    public function index(Request $request)
    {
        $query = TravelNote::with(['teaserImage', 'author'])->latestFirst();

        if ($search = $request->get('q')) {
            $query->search($search);
        }

        $notes = $query->paginate(25)->appends($request->only('q'));

        return view('admin.travel-notes.index', [
            'title' => "Bryan's Travel Notes",
            'notes' => $notes,
            'search' => $search,
        ]);
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $authors = \App\Models\Author::ordered()->get();

        return view('admin.travel-notes.form', [
            'title' => 'Add New Travel Note',
            'note' => new TravelNote(),
            'authors' => $authors,
        ]);
    }

    /**
     * Store a new travel note.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'author_id'          => ['nullable', 'exists:authors,id'],
            'title'              => ['required', 'string', 'max:500'],
            'slug'               => ['nullable', 'string', 'max:255'],
            'content'            => ['nullable', 'string'],
            'teaser_image_id'    => ['nullable', 'string'],
            'biblical_reference' => ['nullable', 'string', 'max:255'],
            'location'           => ['nullable', 'string', 'max:255'],
            'sort_order'         => ['nullable', 'integer'],
            'published'          => ['boolean'],
            'published_at'       => ['nullable', 'date'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = TravelNote::generateUniqueSlug($validated['title']);
        }

        $validated['published'] = $request->has('published');
        if ($validated['published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $validated['content'] = !empty($validated['content']) ? $validated['content'] : null;

        // Handle URL-based teaser: if the value starts with "url:", store null as image_id
        // (the URL is stored in a tiny cache or we just drop it — for simplicity we ignore URL teasers
        // and require library images. If needed later we can add a teaser_url column.)
        if (!empty($validated['teaser_image_id']) && str_starts_with($validated['teaser_image_id'], 'url:')) {
            $validated['teaser_image_id'] = null;
        }
        $validated['teaser_image_id'] = $validated['teaser_image_id'] ?: null;

        TravelNote::create($validated);

        return redirect()
            ->route('admin.travel-notes.index')
            ->with('status', "Travel note \"{$validated['title']}\" created successfully.");
    }

    /**
     * Show edit form.
     */
    public function edit(TravelNote $note)
    {
        $note->load(['teaserImage', 'author']);

        $authors = \App\Models\Author::ordered()->get();

        return view('admin.travel-notes.form', [
            'title' => "Edit: {$note->title}",
            'note' => $note,
            'authors' => $authors,
        ]);
    }

    /**
     * Update a travel note.
     */
    public function update(Request $request, TravelNote $note)
    {
        $validated = $request->validate([
            'author_id'          => ['nullable', 'exists:authors,id'],
            'title'              => ['required', 'string', 'max:500'],
            'slug'               => ['nullable', 'string', 'max:255'],
            'content'            => ['nullable', 'string'],
            'teaser_image_id'    => ['nullable', 'string'],
            'biblical_reference' => ['nullable', 'string', 'max:255'],
            'location'           => ['nullable', 'string', 'max:255'],
            'sort_order'         => ['nullable', 'integer'],
            'published'          => ['boolean'],
            'published_at'       => ['nullable', 'date'],
        ]);

        $newSlug = $validated['slug'] ?? null;
        if (empty($newSlug)) {
            $newSlug = TravelNote::generateUniqueSlug($validated['title'], $note->id);
        }
        if ($newSlug !== $note->slug) {
            $note->slug = $newSlug;
        }

        $note->title = $validated['title'];
        $note->author_id = $validated['author_id'] ?? null;
        $note->content = !empty($validated['content']) ? $validated['content'] : null;
        // Handle URL-based teaser
        $teaserVal = $validated['teaser_image_id'] ?? null;
        if ($teaserVal && str_starts_with($teaserVal, 'url:')) {
            $note->teaser_image_id = null;
        } else {
            $note->teaser_image_id = $teaserVal ?: null;
        }
        $note->biblical_reference = $validated['biblical_reference'] ?? null;
        $note->location = $validated['location'] ?? null;
        $note->sort_order = $validated['sort_order'] ?? 0;
        $note->published = $request->has('published');
        $note->published_at = $note->published
            ? ($validated['published_at'] ?? $note->published_at ?? now())
            : null;

        $note->save();

        return redirect()
            ->route('admin.travel-notes.index')
            ->with('status', "Travel note \"{$note->title}\" updated successfully.");
    }

    /**
     * Delete a travel note.
     */
    public function destroy(TravelNote $note)
    {
        $title = $note->title;
        $note->delete();

        return redirect()
            ->route('admin.travel-notes.index')
            ->with('status', "Travel note \"{$title}\" deleted.");
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorAdminController extends Controller
{
    /**
     * Authors dashboard — list all authors ordered by priority.
     */
    public function index()
    {
        $authors = Author::orderBy('priority', 'asc')
            ->orderBy('last_name', 'asc')
            ->orderBy('first_name', 'asc')
            ->paginate(25);

        return view('admin.authors.index', [
            'title' => 'Authors Dashboard',
            'authors' => $authors,
        ]);
    }

    /**
     * Show the form to create a new author.
     */
    public function create()
    {
        return view('admin.authors.form', [
            'title' => 'Add New Author',
            'author' => new Author(),
        ]);
    }

    /**
     * Store a newly created author.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'     => ['required', 'string', 'max:100'],
            'last_name'      => ['required', 'string', 'max:100'],
            'title'          => ['nullable', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:5'],
            'bio'            => ['nullable', 'string'],
            'image'          => ['nullable', 'string', 'max:255'],
            'active'         => ['boolean'],
            'team_member'    => ['boolean'],
            'priority'       => ['nullable', 'integer', 'min:0', 'max:65535'],
            'social_links'   => ['nullable', 'json'],
            'facebook'        => ['nullable', 'string', 'max:500'],
            'youtube'         => ['nullable', 'string', 'max:500'],
            'rumble'          => ['nullable', 'string', 'max:500'],
            'linkedin'        => ['nullable', 'string', 'max:500'],
            'truthsocial'     => ['nullable', 'string', 'max:500'],
            'tiktok'          => ['nullable', 'string', 'max:500'],
            'x'               => ['nullable', 'string', 'max:500'],
        ]);

        // Fix: unchecked checkboxes don't submit any value
        $validated['active'] = $request->has('active');
        $validated['team_member'] = $request->has('team_member');

        if (!isset($validated['priority'])) {
            $validated['priority'] = 0;
        }

        // Handle social_links JSON if provided (legacy field, keeping for backward compat)
        if (!empty($validated['social_links']) && is_string($validated['social_links'])) {
            $decoded = json_decode($validated['social_links'], true);
            $validated['social_links'] = is_array($decoded) ? $decoded : null;
        }

        // Convert empty strings to null for social fields
        foreach (['facebook', 'youtube', 'rumble', 'linkedin', 'truthsocial', 'tiktok', 'x'] as $field) {
            if (isset($validated[$field]) && trim($validated[$field]) === '') {
                $validated[$field] = null;
            }
        }

        $author = Author::create($validated);

        return redirect()
            ->route('admin.authors.index')
            ->with('status', "Author \"{$author->full_name}\" created successfully.");
    }

    /**
     * Show the form to edit an existing author.
     */
    public function edit(Author $author)
    {
        return view('admin.authors.form', [
            'title' => "Edit: {$author->full_name}",
            'author' => $author,
        ]);
    }

    /**
     * Update an existing author.
     */
    public function update(Request $request, Author $author)
    {
        $validated = $request->validate([
            'first_name'     => ['required', 'string', 'max:100'],
            'last_name'      => ['required', 'string', 'max:100'],
            'title'          => ['nullable', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:5'],
            'bio'            => ['nullable', 'string'],
            'image'          => ['nullable', 'string', 'max:255'],
            'active'         => ['boolean'],
            'team_member'    => ['boolean'],
            'priority'       => ['nullable', 'integer', 'min:0', 'max:65535'],
            'social_links'   => ['nullable', 'json'],
            'facebook'        => ['nullable', 'string', 'max:500'],
            'youtube'         => ['nullable', 'string', 'max:500'],
            'rumble'          => ['nullable', 'string', 'max:500'],
            'linkedin'        => ['nullable', 'string', 'max:500'],
            'truthsocial'     => ['nullable', 'string', 'max:500'],
            'tiktok'          => ['nullable', 'string', 'max:500'],
            'x'               => ['nullable', 'string', 'max:500'],
        ]);

        // Fix: unchecked checkboxes don't submit any value
        $validated['active'] = $request->has('active');
        $validated['team_member'] = $request->has('team_member');

        if (!isset($validated['priority'])) {
            $validated['priority'] = 0;
        }

        // Handle social_links JSON if provided (legacy field)
        if (!empty($validated['social_links']) && is_string($validated['social_links'])) {
            $decoded = json_decode($validated['social_links'], true);
            $validated['social_links'] = is_array($decoded) ? $decoded : null;
        }

        // Convert empty strings to null for social fields
        foreach (['facebook', 'youtube', 'rumble', 'linkedin', 'truthsocial', 'tiktok', 'x'] as $field) {
            if (isset($validated[$field]) && trim($validated[$field]) === '') {
                $validated[$field] = null;
            }
        }

        $author->update($validated);

        return redirect()
            ->route('admin.authors.index')
            ->with('status', "Author \"{$author->full_name}\" updated successfully.");
    }

    /**
     * Delete (soft-delete) an author.
     */
    public function destroy(Author $author)
    {
        $name = $author->full_name;
        $author->delete();

        return redirect()
            ->route('admin.authors.index')
            ->with('status', "Author \"{$name}\" deleted.");
    }
}

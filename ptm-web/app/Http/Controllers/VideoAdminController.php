<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VideoAdminController extends Controller
{
    /**
     * Videos dashboard — grid with search + category filter.
     */
    public function index(Request $request)
    {
        $query = Video::latestFirst();

        if ($search = $request->get('q')) {
            $query->search($search);
        }
        if ($cat = $request->get('category')) {
            $query->category($cat);
        }

        $videos = $query->paginate(24)->appends($request->only(['q', 'category']));
        $categories = Video::whereNotNull('category')->distinct()->pluck('category')->sort();

        return view('admin.videos.index', [
            'title' => 'Videos',
            'videos' => $videos,
            'search' => $search,
            'category' => $cat,
            'categories' => $categories,
        ]);
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.videos.form', [
            'title' => 'Add New Video',
            'video' => new Video(),
        ]);
    }

    /**
     * Store a new video.
     */
    public function store(Request $request)
    {
        $mode = $request->input('source_mode', 'upload');

        if ($mode === 'url') {
            $validated = $request->validate([
                'title'       => ['required', 'string', 'max:500'],
                'slug'        => ['nullable', 'string', 'max:255'],
                'source_url'  => ['required', 'string', 'max:2000'],
                'description' => ['nullable', 'string'],
                'category'    => ['nullable', 'string', 'max:255'],
                'published'   => ['boolean'],
            ]);

            $parsed = Video::parseSourceUrl($validated['source_url']);
            $slug = $validated['slug'] ?: Video::generateUniqueSlug($validated['title']);

            Video::create([
                'slug'            => $slug,
                'title'           => $validated['title'],
                'source_url'      => $validated['source_url'],
                'source_platform' => $parsed['platform'],
                'source_id'       => $parsed['id'],
                'description'     => $validated['description'] ?? null,
                'category'        => $validated['category'] ?? null,
                'published'       => $request->has('published'),
            ]);

            return redirect()->route('admin.videos.index')
                ->with('status', "Video \"{$validated['title']}\" added from URL.");
        }

        // Upload mode
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:500'],
            'slug'        => ['nullable', 'string', 'max:255'],
            'video_file'  => ['required', 'file', 'max:512000'], // 500MB max
            'description' => ['nullable', 'string'],
            'category'    => ['nullable', 'string', 'max:255'],
            'published'   => ['boolean'],
        ]);

        $file = $request->file('video_file');

        // Capture file size BEFORE move (Apache PrivateTmp fix)
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension() ?: 'mp4';

        $slug = $validated['slug'] ?: Video::generateUniqueSlug($validated['title']);
        $filename = $slug . '.' . $extension;
        $relativePath = 'videos/' . $filename;

        $file->move(public_path('videos'), $filename);

        Video::create([
            'slug'            => $slug,
            'filename'        => $originalName,
            'path'            => $relativePath,
            'title'           => $validated['title'],
            'description'     => $validated['description'] ?? null,
            'category'        => $validated['category'] ?? null,
            'source_platform' => 'local',
            'file_size'       => $fileSize,
            'mime_type'       => $mimeType,
            'published'       => $request->has('published'),
        ]);

        return redirect()->route('admin.videos.index')
            ->with('status', "Video \"{$validated['title']}\" uploaded successfully.");
    }

    /**
     * Show edit form.
     */
    public function edit(Video $video)
    {
        return view('admin.videos.form', [
            'title' => "Edit: {$video->title}",
            'video' => $video,
        ]);
    }

    /**
     * Update video metadata.
     */
    public function update(Request $request, Video $video)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:500'],
            'slug'        => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category'    => ['nullable', 'string', 'max:255'],
            'source_url'  => ['nullable', 'string', 'max:2000'],
            'published'   => ['boolean'],
        ]);

        $newSlug = $validated['slug'] ?: Video::generateUniqueSlug($validated['title'], $video->id);
        if ($newSlug !== $video->slug) {
            if (Video::where('slug', $newSlug)->where('id', '!=', $video->id)->exists()) {
                return back()->withErrors(['slug' => 'Slug already in use.'])->withInput();
            }
            $video->slug = $newSlug;
        }

        $video->title = $validated['title'];
        $video->description = $validated['description'] ?? null;
        $video->category = $validated['category'] ?? null;
        $video->published = $request->has('published');

        // If source_url was changed, re-parse
        if (!empty($validated['source_url']) && $validated['source_url'] !== $video->source_url) {
            $parsed = Video::parseSourceUrl($validated['source_url']);
            $video->source_url = $validated['source_url'];
            $video->source_platform = $parsed['platform'];
            $video->source_id = $parsed['id'];
        }

        $video->save();

        return redirect()->route('admin.videos.index')
            ->with('status', "Video \"{$video->title}\" updated.");
    }

    /**
     * Delete a video.
     */
    public function destroy(Video $video)
    {
        $title = $video->title;

        // Delete local file if exists
        if ($video->path && file_exists(public_path($video->path))) {
            unlink(public_path($video->path));
        }

        $video->delete();

        return redirect()->route('admin.videos.index')
            ->with('status', "Video \"{$title}\" deleted.");
    }

    /**
     * Search endpoint for CKEditor / picker integrations.
     */
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));
        $query = Video::published()->latestFirst();

        if ($q !== '') {
            $query->search($q);
        }

        return response()->json(
            $query->limit(50)->get(['id', 'slug', 'title', 'source_platform', 'source_url', 'path', 'thumbnail_path'])
                ->map(fn($v) => [
                    'id'     => $v->id,
                    'slug'   => $v->slug,
                    'title'  => $v->title,
                    'thumb'  => $v->thumbnail_url,
                    'url'    => $v->url,
                    'embed'  => $v->embed_url,
                    'local'  => $v->is_local,
                ])
        );
    }
}

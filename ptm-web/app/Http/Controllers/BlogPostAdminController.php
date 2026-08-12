<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogPostAdminController extends Controller
{
    /**
     * Blog posts dashboard — list all posts ordered by published_at desc.
     */
    public function index()
    {
        $posts = BlogPost::with('author')
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->paginate(25);

        return view('admin.blog.index', [
            'title' => 'Blog Posts Dashboard',
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form to create a new blog post.
     */
    public function create()
    {
        $authors = Author::active()->ordered()->get();

        return view('admin.blog.form', [
            'title' => 'Add New Blog Post',
            'post' => new BlogPost(),
            'authors' => $authors,
        ]);
    }

    /**
     * Store a newly created blog post.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'author_id'      => ['nullable', 'exists:authors,id'],
            'title'          => ['required', 'string', 'max:255'],
            'slug'           => ['nullable', 'string', 'max:255', 'unique:blog_posts,slug'],
            'content'        => ['required', 'string'],
            'excerpt'        => ['nullable', 'string'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'published'      => ['boolean'],
            'published_at'   => ['nullable', 'date'],
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            // Ensure uniqueness
            $count = 1;
            $baseSlug = $validated['slug'];
            while (BlogPost::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = "{$baseSlug}-{$count}";
                $count++;
            }
        }

        // Fix: unchecked checkbox
        $validated['published'] = $request->has('published');

        // If published but no published_at, set to now
        if ($validated['published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $post = BlogPost::create($validated);

        return redirect()
            ->route('admin.blog.index')
            ->with('status', "Blog post \"{$post->title}\" created successfully.");
    }

    /**
     * Show the form to edit an existing blog post.
     */
    public function edit(BlogPost $post)
    {
        $authors = Author::active()->ordered()->get();

        return view('admin.blog.form', [
            'title' => "Edit: {$post->title}",
            'post' => $post,
            'authors' => $authors,
        ]);
    }

    /**
     * Update an existing blog post.
     */
    public function update(Request $request, BlogPost $post)
    {
        $validated = $request->validate([
            'author_id'      => ['nullable', 'exists:authors,id'],
            'title'          => ['required', 'string', 'max:255'],
            'slug'           => ['nullable', 'string', 'max:255', 'unique:blog_posts,slug,' . $post->id],
            'content'        => ['required', 'string'],
            'excerpt'        => ['nullable', 'string'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'published'      => ['boolean'],
            'published_at'   => ['nullable', 'date'],
        ]);

        // Auto-generate slug if empty
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Fix: unchecked checkbox
        $validated['published'] = $request->has('published');

        // If published but no published_at, set to now
        if ($validated['published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        // If unchecking published, don't change published_at (keep the historical date)
        if (!$validated['published'] && empty($validated['published_at'])) {
            unset($validated['published_at']);
        }

        $post->update($validated);

        return redirect()
            ->route('admin.blog.index')
            ->with('status', "Blog post \"{$post->title}\" updated successfully.");
    }

    /**
     * Delete (soft-delete) a blog post.
     */
    public function destroy(BlogPost $post)
    {
        $title = $post->title;
        $post->delete();

        return redirect()
            ->route('admin.blog.index')
            ->with('status', "Blog post \"{$title}\" deleted.");
    }
}

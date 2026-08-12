<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Blog index — display all published posts, paginated, with optional search.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('q', ''));

        $query = BlogPost::with('author')
            ->published()
            ->latestFirst();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(9)->appends(['q' => $search]);

        return view('blog.index', [
            'title' => 'Truths Revealed Blog',
            'posts' => $posts,
            'search' => $search,
        ]);
    }

    /**
     * Show a single blog post by slug.
     */
    public function show(string $slug)
    {
        $post = BlogPost::with('author')
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Get 3 related posts (excluding current)
        $related = BlogPost::with('author')
            ->published()
            ->where('id', '!=', $post->id)
            ->latestFirst()
            ->limit(3)
            ->get();

        return view('blog.show', [
            'title' => $post->title,
            'post' => $post,
            'related' => $related,
        ]);
    }
}

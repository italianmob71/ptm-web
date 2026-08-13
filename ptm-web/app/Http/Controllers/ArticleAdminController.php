<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleAdminController extends Controller
{
    /**
     * Articles dashboard — list with search.
     */
    public function index(Request $request)
    {
        $query = Article::with('author')->latestFirst();

        if ($search = $request->get('q')) {
            $query->search($search);
        }

        $articles = $query->paginate(25)->appends($request->only('q'));

        return view('admin.articles.index', [
            'title' => 'Articles Dashboard',
            'articles' => $articles,
            'search' => $search,
        ]);
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $authors = Author::orderBy('last_name')->orderBy('first_name')->get();

        return view('admin.articles.form', [
            'title' => 'Add New Article',
            'article' => new Article(),
            'authors' => $authors,
        ]);
    }

    /**
     * Store a new article.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:500'],
            'author_id'      => ['nullable', 'exists:authors,id'],
            'slug'           => ['nullable', 'string', 'max:255'],
            'summary'       => ['nullable', 'string', 'max:1000'],
            'content'       => ['nullable', 'string'],
            'published'     => ['boolean'],
            'published_at'  => ['nullable', 'date'],
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Article::generateUniqueSlug($validated['title']);
        }

        // Handle NOSEARCH- prefix
        if ($request->has('nosearch')) {
            if (!str_starts_with($validated['slug'], 'NOSEARCH-')) {
                $validated['slug'] = 'NOSEARCH-' . $validated['slug'];
            }
        }

        // Checkbox fix
        $validated['published'] = $request->has('published');

        if ($validated['published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        // content nullable — can be null for PDF-only style articles (no text)
        $validated['content'] = !empty($validated['content']) ? $validated['content'] : null;

        $article = Article::create($validated);

        return redirect()
            ->route('admin.articles.index')
            ->with('status', "Article \"{$article->title}\" created successfully.");
    }

    /**
     * Show edit form.
     */
    public function edit(Article $article)
    {
        $authors = Author::orderBy('last_name')->orderBy('first_name')->get();

        return view('admin.articles.form', [
            'title' => "Edit: {$article->title}",
            'article' => $article,
            'authors' => $authors,
        ]);
    }

    /**
     * Update an article.
     */
    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:500'],
            'author_id'      => ['nullable', 'exists:authors,id'],
            'slug'           => ['nullable', 'string', 'max:255'],
            'summary'       => ['nullable', 'string', 'max:1000'],
            'content'       => ['nullable', 'string'],
            'published'     => ['boolean'],
            'published_at'  => ['nullable', 'date'],
        ]);

        // Handle slug
        $newSlug = $validated['slug'] ?? null;
        if (empty($newSlug)) {
            $newSlug = Article::generateUniqueSlug($validated['title'], $article->id);
        }

        // Handle NOSEARCH- prefix
        if ($request->has('nosearch')) {
            if (!str_starts_with($newSlug, 'NOSEARCH-')) {
                $newSlug = 'NOSEARCH-' . $newSlug;
            }
        } else {
            // Strip NOSEARCH- if unchecked
            if (str_starts_with($newSlug, 'NOSEARCH-')) {
                $newSlug = substr($newSlug, strlen('NOSEARCH-'));
            }
        }

        if ($newSlug !== $article->slug) {
            if (Article::where('slug', $newSlug)->where('id', '!=', $article->id)->exists()) {
                return back()->withErrors(['slug' => 'Slug already in use.'])->withInput();
            }
            $article->slug = $newSlug;
        }

        $article->title = $validated['title'];
        $article->author_id = $validated['author_id'] ?? null;
        $article->summary = $validated['summary'] ?? null;
        $article->content = !empty($validated['content']) ? $validated['content'] : null;

        // Checkbox fix
        $article->published = $request->has('published');
        $article->published_at = $article->published
            ? ($validated['published_at'] ?? $article->published_at ?? now())
            : null;

        $article->save();

        return redirect()
            ->route('admin.articles.index')
            ->with('status', "Article \"{$article->title}\" updated successfully.");
    }

    /**
     * Delete (soft-delete) an article.
     */
    public function destroy(Article $article)
    {
        $title = $article->title;
        $article->delete();

        return redirect()
            ->route('admin.articles.index')
            ->with('status', "Article \"{$title}\" deleted.");
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Articles index — display all published, searchable articles.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('q', ''));

        $query = Article::with('author')
            ->published()
            ->latestFirst();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $articles = $query->paginate(9)->appends(['q' => $search]);

        return view('articles.index', [
            'title' => 'Articles',
            'articles' => $articles,
            'search' => $search,
        ]);
    }

    /**
     * Show a single article by slug.
     */
    public function show(string $slug)
    {
        $article = Article::with('author')
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Get 3 related articles (excluding current)
        $related = Article::with('author')
            ->published()
            ->where('id', '!=', $article->id)
            ->latestFirst()
            ->limit(3)
            ->get();

        return view('articles.show', [
            'title' => $article->title,
            'article' => $article,
            'related' => $related,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookAdminController extends Controller
{
    /**
     * Books dashboard — list all books ordered by priority.
     */
    public function index()
    {
        $books = Book::with('author')
            ->orderBy('priority', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('admin.books.index', [
            'title' => 'Books Dashboard',
            'books' => $books,
        ]);
    }

    /**
     * Show the form to create a new book.
     */
    public function create()
    {
        $authors = Author::active()->ordered()->get();

        return view('admin.books.form', [
            'title' => 'Add New Book',
            'book' => new Book(),
            'authors' => $authors,
        ]);
    }

    /**
     * Store a newly created book.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'subtitle'     => ['nullable', 'string', 'max:255'],
            'slug'         => ['nullable', 'string', 'max:255'],
            'author_id'    => ['required', 'exists:authors,id'],
            'body'         => ['nullable', 'string'],
            'isbn_13'      => ['nullable', 'string', 'max:20'],
            'isbn_10'      => ['nullable', 'string', 'max:20'],
            'amazon_link'  => ['nullable', 'string', 'max:500'],
            'lulu_link'    => ['nullable', 'string', 'max:500'],
            'image_front'  => ['nullable', 'string', 'max:255'],
            'image_back'   => ['nullable', 'string', 'max:255'],
            'image_inner'  => ['nullable', 'string', 'max:255'],
            'edition'      => ['nullable', 'string', 'max:100'],
            'published_at' => ['nullable', 'date'],
            'published'    => ['boolean'],
            'active'       => ['boolean'],
            'priority'     => ['nullable', 'integer', 'min:0', 'max:65535'],
            'page_count'   => ['nullable', 'integer'],
            'language'     => ['nullable', 'string', 'max:50'],
            'price_usd'    => ['nullable', 'numeric'],
        ]);

        // Fix: unchecked checkboxes don't submit any value, so force them to false
        $validated['published'] = $request->has('published');
        $validated['active'] = $request->has('active');

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if (empty($validated['image_front'])) {
            $validated['image_front'] = 'default-book.jpg';
        }

        if (!isset($validated['priority'])) {
            $validated['priority'] = 0;
        }

        $book = Book::create($validated);

        return redirect()
            ->route('admin.books.index')
            ->with('status', "Book \"{$book->title}\" created successfully.");
    }

    /**
     * Show the form to edit an existing book.
     */
    public function edit(Book $book)
    {
        $authors = Author::active()->ordered()->get();

        return view('admin.books.form', [
            'title' => "Edit: {$book->title}",
            'book' => $book,
            'authors' => $authors,
        ]);
    }

    /**
     * Update an existing book.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'subtitle'     => ['nullable', 'string', 'max:255'],
            'slug'         => ['nullable', 'string', 'max:255'],
            'author_id'    => ['required', 'exists:authors,id'],
            'body'         => ['nullable', 'string'],
            'isbn_13'      => ['nullable', 'string', 'max:20'],
            'isbn_10'      => ['nullable', 'string', 'max:20'],
            'amazon_link'  => ['nullable', 'string', 'max:500'],
            'lulu_link'    => ['nullable', 'string', 'max:500'],
            'image_front'  => ['nullable', 'string', 'max:255'],
            'image_back'   => ['nullable', 'string', 'max:255'],
            'image_inner'  => ['nullable', 'string', 'max:255'],
            'edition'      => ['nullable', 'string', 'max:100'],
            'published_at' => ['nullable', 'date'],
            'published'    => ['boolean'],
            'active'       => ['boolean'],
            'priority'     => ['nullable', 'integer', 'min:0', 'max:65535'],
            'page_count'   => ['nullable', 'integer'],
            'language'     => ['nullable', 'string', 'max:50'],
            'price_usd'    => ['nullable', 'numeric'],
        ]);

        // Fix: unchecked checkboxes don't submit any value, so force them to false
        $validated['published'] = $request->has('published');
        $validated['active'] = $request->has('active');

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if (!isset($validated['priority'])) {
            $validated['priority'] = 0;
        }

        $book->update($validated);

        return redirect()
            ->route('admin.books.index')
            ->with('status', "Book \"{$book->title}\" updated successfully.");
    }

    /**
     * Delete (soft-delete) a book.
     */
    public function destroy(Book $book)
    {
        $title = $book->title;
        $book->delete();

        return redirect()
            ->route('admin.books.index')
            ->with('status', "Book \"{$title}\" deleted.");
    }
}

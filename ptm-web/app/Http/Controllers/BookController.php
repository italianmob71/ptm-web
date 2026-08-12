<?php

namespace App\Http\Controllers;

use App\Models\Book;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::where('published', true)
            ->where('active', true)
            ->with('author')
            ->ordered()
            ->paginate(12);

        return view('books.index', [
            'title' => 'Book Recommendations',
            'books' => $books,
        ]);
    }

    public function show(Book $book)
    {
        $book->load('author');

        // Ensure the book is published and active
        if (!$book->published || !$book->active) {
            abort(404);
        }

        // Get other books by same author
        $otherBooks = Book::where('published', true)
            ->where('active', true)
            ->where('author_id', $book->author_id)
            ->where('id', '!=', $book->id)
            ->ordered()
            ->take(4)
            ->get();

        return view('books.show', [
            'title' => $book->full_title,
            'book' => $book,
            'otherBooks' => $otherBooks,
        ]);
    }
}

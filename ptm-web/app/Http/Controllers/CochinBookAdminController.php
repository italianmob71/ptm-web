<?php

namespace App\Http\Controllers;

use App\Models\CochinBook;
use App\Models\CochinChapter;
use App\Models\Pdf;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CochinBookAdminController extends Controller
{
    /**************************************************************************
     * BOOK CRUD
     *************************************************************************/

    public function index(Request $request)
    {
        $query = CochinBook::with(['chapters', 'coverImage', 'completePdf'])->ordered();

        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->where(function ($q) use ($term) {
                $q->where('title', 'LIKE', "%{$term}%")
                  ->orWhere('slug', 'LIKE', "%{$term}%")
                  ->orWhere('manuscript', 'LIKE', "%{$term}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $books = $query->paginate(12)->appends($request->only(['search', 'status']));

        return view('admin.cochin-books.index', [
            'title' => 'Cochin Books',
            'books' => $books,
        ]);
    }

    public function create()
    {
        $images = Image::latestFirst()->limit(100)->get(['id', 'filename', 'alt_text']);
        $pdfs = Pdf::latestFirst()->limit(100)->get(['id', 'filename', 'title']);

        return view('admin.cochin-books.form', [
            'title' => 'Add New Cochin Book',
            'book' => new CochinBook(),
            'images' => $images,
            'pdfs' => $pdfs,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'slug'            => ['nullable', 'string', 'max:100'],
            'manuscript'      => ['nullable', 'string', 'max:100'],
            'description'     => ['nullable', 'string'],
            'discoveries'     => ['nullable', 'string'],
            'status'          => ['required', 'in:wip,complete'],
            'display_order'   => ['nullable', 'integer', 'min:1', 'max:999'],
            'total_chapters'  => ['nullable', 'integer', 'min:0', 'max:200'],
            'cover_image_id'  => ['nullable', 'exists:images,id'],
            'complete_pdf_id' => ['nullable', 'exists:pdfs,id'],
            'published'       => ['nullable'],
        ]);

        $book = new CochinBook();
        $book->title = $validated['title'];
        $book->slug = $validated['slug'] ?: CochinBook::generateUniqueSlug($validated['title']);
        // Auto-assign canonical display order from the slug
        $book->display_order = CochinBook::canonicalOrderForSlug($book->slug);
        $book->manuscript = $validated['manuscript'] ?? null;
        $book->description = $validated['description'] ?? null;
        $book->discoveries = $validated['discoveries'] ?? null;
        $book->status = $validated['status'];
        $book->total_chapters = $validated['total_chapters'] ?? 0;
        $book->cover_image_id = $validated['cover_image_id'] ?? null;
        $book->complete_pdf_id = $validated['complete_pdf_id'] ?? null;
        $book->published = $request->has('published');
        $book->published_at = $book->published ? now() : null;
        $book->save();

        return redirect()->route('admin.cochin-books.edit', $book)
            ->with('success', "Book '{$book->title}' created. Now add chapters below.");
    }

    public function edit(CochinBook $book)
    {
        $book->load(['chapters.pdf', 'chapters.video', 'coverImage', 'completePdf']);

        $images = Image::latestFirst()->limit(100)->get(['id', 'filename', 'alt_text']);
        $pdfs = Pdf::latestFirst()->limit(100)->get(['id', 'filename', 'title']);

        return view('admin.cochin-books.form', [
            'title' => "Edit: {$book->title}",
            'book' => $book,
            'images' => $images,
            'pdfs' => $pdfs,
        ]);
    }

    public function update(Request $request, CochinBook $book)
    {
        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'slug'            => ['nullable', 'string', 'max:100'],
            'manuscript'      => ['nullable', 'string', 'max:100'],
            'description'     => ['nullable', 'string'],
            'discoveries'     => ['nullable', 'string'],
            'status'          => ['required', 'in:wip,complete'],
            'display_order'   => ['nullable', 'integer', 'min:1', 'max:999'],
            'total_chapters'  => ['nullable', 'integer', 'min:0', 'max:200'],
            'cover_image_id'  => ['nullable', 'exists:images,id'],
            'complete_pdf_id' => ['nullable', 'exists:pdfs,id'],
            'published'       => ['nullable'],
        ]);

        $book->title = $validated['title'];
        $book->slug = $validated['slug'] ?: $book->slug;
        // Re-derive canonical display order if slug changed or if manually provided
        if (!empty($validated['display_order'])) {
            $book->display_order = $validated['display_order'];
        } elseif ($book->wasChanged('slug')) {
            $book->display_order = CochinBook::canonicalOrderForSlug($book->slug);
        }
        $book->manuscript = $validated['manuscript'] ?? null;
        $book->description = $validated['description'] ?? null;
        $book->discoveries = $validated['discoveries'] ?? null;
        $book->status = $validated['status'];
        $book->total_chapters = $validated['total_chapters'] ?? 0;
        $book->cover_image_id = $validated['cover_image_id'] ?? null;
        $book->complete_pdf_id = $validated['complete_pdf_id'] ?? null;
        $book->published = $request->has('published');
        $book->published_at = $book->published ? ($book->published_at ?? now()) : null;
        $book->save();

        return redirect()->route('admin.cochin-books.edit', $book)
            ->with('success', "Book '{$book->title}' updated.");
    }

    public function destroy(CochinBook $book)
    {
        $title = $book->title;
        $book->delete();
        return redirect()->route('admin.cochin-books.index')
            ->with('success', "Book '{$title}' deleted.");
    }

    /**************************************************************************
     * CHAPTER MANAGEMENT (AJAX-style within the book edit page)
     *************************************************************************/

    public function storeChapter(Request $request, CochinBook $book)
    {
        $validated = $request->validate([
            'chapter_number' => ['required', 'integer', 'min:1', 'max:200'],
            'title'          => ['nullable', 'string', 'max:255'],
            'pdf_id'         => ['nullable', 'exists:pdfs,id'],
            'chapter_pdf'    => ['nullable', 'file', 'max:51200'],
            'published'      => ['nullable'],
        ]);

        // Prevent duplicate chapter numbers
        $exists = $book->chapters()->where('chapter_number', $validated['chapter_number'])->exists();
        if ($exists) {
            return back()->withErrors(['chapter_number' => "Chapter {$validated['chapter_number']} already exists."]);
        }

        $pdfId = $validated['pdf_id'] ?? null;

        // Handle drag-drop PDF upload: save file, create Pdf record
        if ($request->hasFile('chapter_pdf')) {
            $uploadedPdf = $request->file('chapter_pdf');
            $fileSize = $uploadedPdf->getSize();
            $mime = $uploadedPdf->getMimeType();
            if ($mime !== 'application/pdf') {
                return back()->withErrors(['chapter_pdf' => 'File must be a PDF.'])->withInput();
            }
            $filename = Str::slug(pathinfo($uploadedPdf->getClientOriginalName(), PATHINFO_FILENAME)) . '.pdf';
            $uploadedPdf->move(public_path('pdfs'), $filename);

            $pdf = new Pdf();
            $pdf->filename = $filename;
            $pdf->path = 'pdfs/' . $filename;
            $pdf->slug = Pdf::generateUniqueSlug($book->title . '-chapter-' . $validated['chapter_number']);
            $pdf->title = $book->title . ' Chapter ' . $validated['chapter_number'];
            $pdf->file_size = $fileSize;
            $pdf->mime_type = 'application/pdf';
            $pdf->category = 'Cochin ' . $book->title;
            $pdf->save();
            $pdfId = $pdf->id;
        }

        $chapter = new CochinChapter();
        $chapter->book_id = $book->id;
        $chapter->chapter_number = $validated['chapter_number'];
        $chapter->title = $validated['title'] ?? null;
        $chapter->pdf_id = $pdfId;
        $chapter->published = $request->has('published');
        $chapter->published_at = $chapter->published ? now() : null;
        $chapter->save();

        return redirect()->route('admin.cochin-books.edit', $book)
            ->with('success', "Chapter {$chapter->chapter_number} added.");
    }

    public function updateChapter(Request $request, CochinBook $book, CochinChapter $chapter)
    {
        $validated = $request->validate([
            'chapter_number' => ['required', 'integer', 'min:1', 'max:200'],
            'title'          => ['nullable', 'string', 'max:255'],
            'pdf_id'         => ['nullable', 'exists:pdfs,id'],
            'published'      => ['nullable'],
        ]);

        $chapter->chapter_number = $validated['chapter_number'];
        $chapter->title = $validated['title'] ?? null;
        $chapter->pdf_id = $validated['pdf_id'] ?? null;
        $chapter->published = $request->has('published');
        $chapter->published_at = $chapter->published ? ($chapter->published_at ?? now()) : null;
        $chapter->save();

        return redirect()->route('admin.cochin-books.edit', $book)
            ->with('success', "Chapter {$chapter->chapter_number} updated.");
    }

    public function destroyChapter(CochinBook $book, CochinChapter $chapter)
    {
        $num = $chapter->chapter_number;
        $chapter->delete();
        return redirect()->route('admin.cochin-books.edit', $book)
            ->with('success', "Chapter {$num} removed.");
    }
}

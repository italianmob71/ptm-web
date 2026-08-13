<?php

namespace App\Http\Controllers;

use App\Models\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PdfAdminController extends Controller
{
    private const PDF_DIR = 'pdfs';
    private const ALLOWED_MIMES = [
        'application/pdf',
        'application/x-pdf',
        'application/octet-stream',
    ];
    private const MAX_SIZE = 52428800; // 50MB

    /**
     * PDFs dashboard — list with search and category filter.
     */
    public function index(Request $request)
    {
        $query = Pdf::latestFirst();

        if ($search = $request->get('q')) {
            $query->search($search);
        }

        if ($category = $request->get('category')) {
            $query->category($category);
        }

        $pdfs = $query->paginate(24)->appends($request->only(['q', 'category']));
        $categories = Pdf::categories();

        return view('admin.pdfs.index', [
            'title' => 'PDFs Dashboard',
            'pdfs' => $pdfs,
            'search' => $search,
            'categories' => $categories,
            'currentCategory' => $category,
        ]);
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $categories = Pdf::categories();

        return view('admin.pdfs.form', [
            'title' => 'Upload PDF',
            'pdf' => new Pdf(),
            'categories' => $categories,
        ]);
    }

    /**
     * Store a new PDF — upload or URL import.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pdf_type'  => ['required', 'in:upload,url'],
            'pdf_file'  => ['nullable', 'file', 'max:51200'],
            'pdf_url'   => ['nullable', 'string', 'max:500'],
            'title'     => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'category'  => ['nullable', 'string', 'max:100'],
        ]);

        $pdfPath = null;
        $filename = null;
        $fileSize = null;
        $mimeType = null;
        $sourceUrl = null;

        if ($validated['pdf_type'] === 'upload') {
            $pdf = $request->file('pdf_file');
            if (!$pdf) {
                return back()->withErrors(['pdf_file' => 'Please select a PDF file.'])->withInput();
            }

            $mime = $pdf->getMimeType();
            $fileSize = $pdf->getSize(); // Capture BEFORE move

            if (!in_array($mime, self::ALLOWED_MIMES)) {
                return back()->withErrors(['pdf_file' => 'File must be a PDF. Detected: ' . $mime])->withInput();
            }
            if ($fileSize > self::MAX_SIZE) {
                return back()->withErrors(['pdf_file' => 'PDF must be under 50MB.'])->withInput();
            }

            $filename = Str::slug(pathinfo($pdf->getClientOriginalName(), PATHINFO_FILENAME)) . '.pdf';
            $pdf->move(public_path(self::PDF_DIR), $filename);
            $pdfPath = self::PDF_DIR . '/' . $filename;
            $mimeType = $mime;
        } elseif ($validated['pdf_type'] === 'url') {
            $url = $validated['pdf_url'] ?? '';
            if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
                return back()->withErrors(['pdf_url' => 'Please provide a valid URL.'])->withInput();
            }

            $sourceUrl = $url;
            $filename = Str::slug(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_FILENAME)) . '.pdf';
            if (empty($filename) || $filename === '.pdf') {
                $filename = 'pdf-' . time() . '.pdf';
            }

            $targetPath = public_path(self::PDF_DIR) . '/' . $filename;
            $context = stream_context_create(['http' => ['timeout' => 30]]);
            $data = @file_get_contents($url, false, $context);

            if ($data === false) {
                return back()->withErrors(['pdf_url' => 'Could not download PDF from URL.'])->withInput();
            }

            file_put_contents($targetPath, $data);
            $pdfPath = self::PDF_DIR . '/' . $filename;
            $fileSize = filesize($targetPath);
            $mimeType = mime_content_type($targetPath);

            if (!in_array($mimeType, self::ALLOWED_MIMES)) {
                unlink($targetPath);
                return back()->withErrors(['pdf_url' => 'Downloaded file is not a PDF. Detected: ' . $mimeType])->withInput();
            }
        }

        $pdf = Pdf::create([
            'slug'        => Pdf::generateUniqueSlug($filename),
            'filename'    => $filename,
            'path'        => $pdfPath,
            'title'       => $validated['title'] ?? null,
            'description' => $validated['description'] ?? null,
            'category'    => $validated['category'] ?? null,
            'file_size'   => $fileSize,
            'mime_type'   => $mimeType,
            'source_url'  => $sourceUrl,
        ]);

        return redirect()
            ->route('admin.pdfs.index')
            ->with('status', "PDF \"{$pdf->slug}\" uploaded successfully.");
    }

    /**
     * Show edit form.
     */
    public function edit(Pdf $pdf)
    {
        $categories = Pdf::categories();

        return view('admin.pdfs.form', [
            'title' => "Edit: {$pdf->slug}",
            'pdf' => $pdf,
            'categories' => $categories,
        ]);
    }

    /**
     * Update metadata (slug, title, description, category).
     */
    public function update(Request $request, Pdf $pdf)
    {
        $validated = $request->validate([
            'slug'        => ['nullable', 'string', 'max:255'],
            'title'       => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'category'    => ['nullable', 'string', 'max:100'],
        ]);

        $newSlug = $validated['slug'] ?? null;
        if ($newSlug && $newSlug !== $pdf->slug) {
            if (Pdf::where('slug', $newSlug)->where('id', '!=', $pdf->id)->exists()) {
                return back()->withErrors(['slug' => 'Slug already in use.'])->withInput();
            }
            $pdf->slug = $newSlug;
        }

        $pdf->title = $validated['title'] ?? null;
        $pdf->description = $validated['description'] ?? null;
        $pdf->category = $validated['category'] ?? null;

        $pdf->save();

        return redirect()
            ->route('admin.pdfs.index')
            ->with('status', "PDF \"{$pdf->slug}\" updated successfully.");
    }

    /**
     * Delete (soft-delete) a PDF and remove the file.
     */
    public function destroy(Pdf $pdf)
    {
        $slug = $pdf->slug;

        if ($pdf->path && file_exists(public_path($pdf->path))) {
            unlink(public_path($pdf->path));
        }

        $pdf->delete();

        return redirect()
            ->route('admin.pdfs.index')
            ->with('status', "PDF \"{$slug}\" deleted.");
    }

    /**
     * CKEditor upload adapter — receives a PDF file via POST,
     * stores it, and returns JSON {uploaded, url, filename}.
     */
    public function ckeditorUpload(Request $request)
    {
        $file = $request->file('upload');
        if (!$file) {
            return response()->json([
                'uploaded' => false,
                'error' => ['message' => 'No file provided.'],
            ], 400);
        }

        $mime = $file->getMimeType();
        $fileSize = $file->getSize(); // Capture BEFORE move

        if (!in_array($mime, self::ALLOWED_MIMES)) {
            return response()->json([
                'uploaded' => false,
                'error' => ['message' => 'File must be a PDF. Detected: ' . $mime],
            ], 422);
        }

        if ($fileSize > self::MAX_SIZE) {
            return response()->json([
                'uploaded' => false,
                'error' => ['message' => 'PDF must be under 50MB.'],
            ], 422);
        }

        $originalName = $file->getClientOriginalName();
        $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.pdf';

        // Ensure unique filename on disk
        $diskPath = public_path(self::PDF_DIR) . '/' . $filename;
        if (file_exists($diskPath)) {
            $base = pathinfo($filename, PATHINFO_FILENAME);
            $count = 2;
            while (file_exists(public_path(self::PDF_DIR) . '/' . $base . '-' . $count . '.pdf')) {
                $count++;
            }
            $filename = $base . '-' . $count . '.pdf';
        }

        $file->move(public_path(self::PDF_DIR), $filename);
        $pdfPath = self::PDF_DIR . '/' . $filename;

        // Store in pdfs table so it shows up in the library
        $pdf = Pdf::create([
            'slug'      => Pdf::generateUniqueSlug($filename),
            'filename'  => $filename,
            'path'      => $pdfPath,
            'title'     => null,
            'file_size' => $fileSize,
            'mime_type' => $mime,
        ]);

        return response()->json([
            'uploaded' => true,
            'url'      => asset($pdfPath),
            'filename' => $originalName,
        ]);
    }

    /**
     * JSON search endpoint for use by other admin screens.
     */
    public function search(Request $request)
    {
        $query = Pdf::latestFirst()->limit(50);

        if ($q = $request->get('q')) {
            $query->search($q);
        }

        if ($cat = $request->get('category')) {
            $query->category($cat);
        }

        return response()->json(
            $query->get(['id', 'slug', 'filename', 'title', 'path', 'category'])
                ->map(fn($p) => [
                    'id'   => $p->id,
                    'slug' => $p->slug,
                    'filename' => $p->filename,
                    'title' => $p->title,
                    'url' => $p->url,
                    'category' => $p->category,
                ])
        );
    }
}

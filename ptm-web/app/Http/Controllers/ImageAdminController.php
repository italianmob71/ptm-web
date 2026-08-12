<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImageAdminController extends Controller
{
    private const UPLOAD_DIR = 'images/uploads';
    private const MAX_FILE_SIZE = 10485760; // 10MB
    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];

    /**
     * Display all images with search and category filter.
     */
    public function index(Request $request)
    {
        $query = Image::latestFirst();

        if ($request->filled('q')) {
            $query->search($request->input('q'));
        }

        if ($request->filled('category')) {
            $query->category($request->input('category'));
        }

        $images = $query->paginate(24)->appends($request->only(['q', 'category']));
        $categories = Image::categories();

        return view('admin.images.index', compact('images', 'categories'));
    }

    /**
     * Show the upload form.
     */
    public function create()
    {
        $categories = Image::categories();
        return view('admin.images.form', ['image' => new Image(), 'categories' => $categories]);
    }

    /**
     * Handle single or multiple file upload.
     * Stores files directly in public/images/uploads/ (no storage symlink needed).
     */
    public function store(Request $request)
    {
        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|image|max:10240',
            'category' => 'nullable|string|max:100',
            'alt_text' => 'nullable|string|max:500',
        ]);

        $uploadBase = public_path(self::UPLOAD_DIR);
        if (!is_dir($uploadBase)) {
            mkdir($uploadBase, 0775, true);
        }

        $uploaded = [];
        $errors = [];

        foreach ($request->file('files') as $file) {
            try {
                $fileSize = $file->getSize();

                if ($fileSize > self::MAX_FILE_SIZE) {
                    $errors[] = $file->getClientOriginalName() . ' exceeds 10MB limit.';
                    continue;
                }

                $mime = $file->getMimeType();
                if (!in_array($mime, self::ALLOWED_MIMES)) {
                    $errors[] = $file->getClientOriginalName() . ' has unsupported type: ' . $mime;
                    continue;
                }

                $slug = Image::generateUniqueSlug($file->getClientOriginalName());
                $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
                $storedName = $slug . '.' . $extension;

                $file->move($uploadBase, $storedName);
                $publicPath = self::UPLOAD_DIR . '/' . $storedName;

                $width = null;
                $height = null;
                if (in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                    $dims = @getimagesize($uploadBase . '/' . $storedName);
                    if ($dims) {
                        $width = $dims[0];
                        $height = $dims[1];
                    }
                }

                $image = Image::create([
                    'slug' => $slug,
                    'filename' => $file->getClientOriginalName(),
                    'path' => $publicPath,
                    'alt_text' => $request->input('alt_text'),
                    'caption' => null,
                    'mime_type' => $mime,
                    'width' => $width,
                    'height' => $height,
                    'file_size' => $fileSize,
                    'category' => $request->input('category'),
                ]);

                $uploaded[] = $image->slug;
            } catch (\Exception $e) {
                $errors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
            }
        }

        if (count($uploaded) > 0) {
            $msg = count($uploaded) . ' image(s) uploaded: ' . implode(', ', $uploaded);
            if (count($errors) > 0) {
                $msg .= '. Errors: ' . implode(' ', $errors);
            }
            return redirect()->route('admin.images.index')->with('success', $msg);
        }

        return redirect()->back()->withErrors($errors)->withInput();
    }

    /**
     * Show edit form for an image (alt text, caption, category, slug).
     */
    public function edit(Image $image)
    {
        $categories = Image::categories();
        return view('admin.images.form', ['image' => $image, 'categories' => $categories]);
    }

    /**
     * Update image metadata.
     */
    public function update(Request $request, Image $image)
    {
        $request->validate([
            'slug' => 'required|string|max:255|unique:images,slug,' . $image->id,
            'alt_text' => 'nullable|string|max:500',
            'caption' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
        ]);

        $oldPath = $image->path;

        if ($request->input('slug') !== $image->slug) {
            $newSlug = $request->input('slug');
            $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
            $newFilename = $newSlug . '.' . $extension;
            $newPath = self::UPLOAD_DIR . '/' . $newFilename;

            $oldDiskPath = public_path($oldPath);
            $newDiskPath = public_path($newPath);

            if (file_exists($oldDiskPath)) {
                rename($oldDiskPath, $newDiskPath);
            }

            $image->filename = $newFilename;
            $image->path = $newPath;
            $image->slug = $newSlug;
        } else {
            $image->slug = $request->input('slug');
        }

        $image->alt_text = $request->input('alt_text');
        $image->caption = $request->input('caption');
        $image->category = $request->input('category');
        $image->save();

        return redirect()->route('admin.images.index')->with('success', 'Image updated: ' . $image->slug);
    }

    /**
     * Delete image record and file from disk.
     */
    public function destroy(Image $image)
    {
        $diskPath = public_path($image->path);
        if (file_exists($diskPath)) {
            unlink($diskPath);
        }

        $slug = $image->slug;
        $image->delete();

        return redirect()->route('admin.images.index')->with('success', 'Image deleted: ' . $slug);
    }

    /**
     * JSON search endpoint for the image picker modal.
     * Returns array of {id, slug, url, alt_text, category}.
     */
    public function search(Request $request)
    {
        $query = Image::latestFirst()->limit(60);

        if ($request->filled('q')) {
            $query->search($request->input('q'));
        }

        $images = $query->get()->map(fn($img) => [
            'id' => $img->id,
            'slug' => $img->slug,
            'url' => asset($img->path),
            'alt_text' => $img->alt_text,
            'category' => $img->category,
        ]);

        return response()->json($images);
    }

    /**
     * CKEditor image upload adapter endpoint.
     * Accepts a single file upload, stores it, returns URL for CKEditor.
     */
    public function ckeditorUpload(Request $request)
    {
        $request->validate([
            'upload' => 'required|file|image|max:10240',
        ]);

        $file = $request->file('upload');

        $mime = $file->getMimeType();
        if (!in_array($mime, self::ALLOWED_MIMES)) {
            return response()->json([
                'uploaded' => false,
                'error' => ['message' => 'Unsupported file type: ' . $mime],
            ], 422);
        }

        $fileSize = $file->getSize();

        $uploadBase = public_path(self::UPLOAD_DIR);
        if (!is_dir($uploadBase)) {
            mkdir($uploadBase, 0775, true);
        }

        $slug = Image::generateUniqueSlug($file->getClientOriginalName());
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $storedName = $slug . '.' . $extension;

        $file->move($uploadBase, $storedName);
        $publicPath = self::UPLOAD_DIR . '/' . $storedName;

        $width = null;
        $height = null;
        if (in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            $dims = @getimagesize($uploadBase . '/' . $storedName);
            if ($dims) {
                $width = $dims[0];
                $height = $dims[1];
            }
        }

        Image::create([
            'slug' => $slug,
            'filename' => $file->getClientOriginalName(),
            'path' => $publicPath,
            'alt_text' => null,
            'caption' => null,
            'mime_type' => $mime,
            'width' => $width,
            'height' => $height,
            'file_size' => $fileSize,
            'category' => 'blog-inline',
        ]);

        $url = asset($publicPath);

        return response()->json([
            'uploaded' => true,
            'url' => $url,
        ]);
    }
}

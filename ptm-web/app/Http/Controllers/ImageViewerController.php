<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;

class ImageViewerController extends Controller
{
    /**
     * Show a single image in a lightbox-style viewer page.
     */
    public function show(string $slug)
    {
        $image = Image::where('slug', $slug)->firstOrFail();

        return view('images.show', [
            'image' => $image,
            'title' => $image->title ?? $image->filename,
        ]);
    }
}

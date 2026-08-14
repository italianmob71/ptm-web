<?php

namespace App\Http\Controllers;

use App\Models\Pdf;
use Illuminate\Http\Request;

class PdfViewerController extends Controller
{
    /**
     * Show a single PDF in an embedded viewer.
     */
    public function show(string $slug)
    {
        $pdf = Pdf::where('slug', $slug)->firstOrFail();

        return view('pdfs.show', [
            'pdf'   => $pdf,
            'title' => $pdf->title ?? $pdf->filename,
        ]);
    }
}

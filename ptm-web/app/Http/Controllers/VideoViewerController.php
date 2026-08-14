<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoViewerController extends Controller
{
    /**
     * Show a single video in an embedded player.
     */
    public function show(string $slug)
    {
        $video = Video::where('slug', $slug)->firstOrFail();

        return view('videos.show', [
            'video' => $video,
            'title' => $video->title ?? $video->filename,
        ]);
    }
}

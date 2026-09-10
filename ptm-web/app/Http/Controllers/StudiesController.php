<?php

namespace App\Http\Controllers;

use App\Models\CochinBook;

class StudiesController extends Controller
{
    public function index()
    {
        return view('studies.index', [
            'title' => 'Living Scroll Studies',
            'cochinBooks' => CochinBook::published()->ordered()->get(['slug', 'title']),
        ]);
    }
}

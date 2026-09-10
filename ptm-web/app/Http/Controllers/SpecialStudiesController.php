<?php

namespace App\Http\Controllers;

class SpecialStudiesController extends Controller
{
    public function index()
    {
        return view('special-studies.index', [
            'title' => 'Special Studies',
        ]);
    }
}
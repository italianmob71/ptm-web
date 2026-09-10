<?php

namespace App\Http\Controllers;

use App\Models\SpecialStudy;
use App\Models\SpecialStudyPage;

class SpecialStudiesController extends Controller
{
    public function index()
    {
        $page = SpecialStudyPage::findOrFail(1);

        return view('special-studies.index', [
            'title' => $page->title,
            'page' => $page,
            'studies' => SpecialStudy::with('pdf')->whereHas('pdf')->orderBy('display_order')->orderBy('id')->get(),
        ]);
    }
}

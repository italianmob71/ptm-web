<?php

namespace App\Http\Controllers;

use App\Models\ResearchStudy;
use App\Models\ResearchStudyPage;

class GetInvolvedInResearchController extends Controller
{
    public function index()
    {
        $page = ResearchStudyPage::findOrFail(1);

        return view('get-involved.index', [
            'title' => $page->title,
            'page' => $page,
            'studies' => ResearchStudy::upcomingOrOngoing()->get(),
        ]);
    }
}

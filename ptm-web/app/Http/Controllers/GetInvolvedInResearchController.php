<?php

namespace App\Http\Controllers;

class GetInvolvedInResearchController extends Controller
{
    public function index()
    {
        return view('get-involved.index', [
            'title' => 'Get Involved in Research!',
        ]);
    }
}
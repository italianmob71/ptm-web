<?php

namespace App\Http\Controllers;

use App\Models\Author;

class TeamController extends Controller
{
    public function index()
    {
        $teamMembers = Author::where('team_member', true)
            ->orderBy('priority')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('team.index', [
            'title' => 'Our Team',
            'teamMembers' => $teamMembers,
        ]);
    }
}
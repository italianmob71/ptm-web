<?php

namespace App\Http\Controllers;

class RenewedCovenantController extends Controller
{
    public function index()
    {
        return view('renewed-covenant.index', [
            'title' => 'The Renewed Covenant',
        ]);
    }
}

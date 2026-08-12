<?php
namespace App\Http\Controllers;

use App\Models\BlogPost;

class LandingPageController extends Controller
{
    public function index()
    {
        $latestPosts = BlogPost::with('author')
            ->published()
            ->latestFirst()
            ->limit(3)
            ->get();

        return view('landing.index', [
            'title' => 'Home',
            'latestPosts' => $latestPosts,
        ]);
    }
}

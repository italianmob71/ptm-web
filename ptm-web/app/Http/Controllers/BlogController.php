<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $posts = [
            [
                'title' => 'The Scroll of Mysteries Unveiled',
                'slug' => 'scroll-of-mysteries-unveiled',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation.',
                'image' => 'revelation-500x500-1.jpg',
                'date' => '2026-07-15',
                'author' => 'Janice F. Baca',
            ],
            [
                'title' => 'Cochin Manuscripts: New Discoveries',
                'slug' => 'cochin-manuscripts-new-discoveries',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation.',
                'image' => 'new-testament-500x500-1.jpg',
                'date' => '2026-07-08',
                'author' => 'Bryan S. Williams',
            ],
            [
                'title' => 'The Covenant of Friendship Explored',
                'slug' => 'covenant-of-friendship-explored',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation.',
                'image' => 'renewed-500x500-1.jpg',
                'date' => '2026-07-01',
                'author' => 'Justin Leoni',
            ],
            [
                'title' => 'Mount Sinai: Archaeological Evidence',
                'slug' => 'mount-sinai-archaeological-evidence',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation.',
                'image' => 'mtSinai-500x500-1.jpg',
                'date' => '2026-06-28',
                'author' => 'Bryan S. Williams',
            ],
            [
                'title' => 'Special Studies: Ancient Hebrew Manuscripts',
                'slug' => 'special-studies-ancient-hebrew-manuscripts',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation.',
                'image' => 'studies-500x500-1.jpg',
                'date' => '2026-06-20',
                'author' => 'Jonathan Meyer',
            ],
            [
                'title' => 'The Renewed Covenant: A Deeper Look',
                'slug' => 'renewed-covenant-deeper-look',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation.',
                'image' => 'renewed-500x500-1.jpg',
                'date' => '2026-06-12',
                'author' => 'Victor Nuñez',
            ],
        ];

        return view('blog.index', [
            'title' => 'Truths Revealed Blog',
            'posts' => $posts,
        ]);
    }
}
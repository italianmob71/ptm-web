<?php

namespace App\Http\Controllers;

class TopicsController extends Controller
{
    public function index()
    {
        $topics = [
            [
                'title' => 'Mount Sinai Evidence',
                'slug' => 'mount-sinai-evidence',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'image' => 'mtSinai-500x500-1.jpg',
            ],
            [
                'title' => 'Special Studies Archive',
                'slug' => 'special-studies-archive',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'image' => 'studies-500x500-1.jpg',
            ],
            [
                'title' => 'Renewed Covenant',
                'slug' => 'renewed-covenant',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'image' => 'renewed-500x500-1.jpg',
            ],
            [
                'title' => 'Scroll of Mysteries',
                'slug' => 'scroll-of-mysteries',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'image' => 'revelation-500x500-1.jpg',
            ],
            [
                'title' => 'Cochin NT Manuscripts',
                'slug' => 'cochin-nt-manuscripts',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'image' => 'new-testament-500x500-1.jpg',
            ],
            [
                'title' => 'Archaeological Discoveries',
                'slug' => 'archaeological-discoveries',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'image' => 'mtSinai-500x500-1.jpg',
            ],
            [
                'title' => 'Hebrew Revelation Studies',
                'slug' => 'hebrew-revelation-studies',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'image' => 'revelation-500x500-1.jpg',
            ],
            [
                'title' => 'Cochin Hebrew James',
                'slug' => 'cochin-hebrew-james',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'image' => 'renewed-500x500-1.jpg',
            ],
            [
                'title' => 'Ancient Manuscript Analysis',
                'slug' => 'ancient-manuscript-analysis',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'image' => 'studies-500x500-1.jpg',
            ],
            [
                'title' => 'Biblical Archaeology',
                'slug' => 'biblical-archaeology',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'image' => 'mtSinai-500x500-1.jpg',
            ],
            [
                'title' => 'Second Temple Hebrew',
                'slug' => 'second-temple-hebrew',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'image' => 'new-testament-500x500-1.jpg',
            ],
            [
                'title' => 'Covenant Theology',
                'slug' => 'covenant-theology',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'image' => 'renewed-500x500-1.jpg',
            ],
        ];

        return view('topics.index', [
            'title' => 'Truth Topics',
            'topics' => $topics,
        ]);
    }
}
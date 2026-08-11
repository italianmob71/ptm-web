<?php

namespace Database\Seeders;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        
        $events = [
            [
                'title' => 'Cochin Manuscript Translation Workshop',
                'description' => 'Monthly workshop on translating the Cochin Hebrew manuscripts with Janice Baca and Bryan Williams.',
                'starts_at' => $now->copy()->startOfMonth()->addDays(5)->setTime(10, 0),
                'ends_at' => $now->copy()->startOfMonth()->addDays(5)->setTime(14, 0),
                'all_day' => false,
                'location' => 'PTM Office / Online',
                'color' => '#f59e0b',
            ],
            [
                'title' => 'Mount Sinai Expedition Briefing',
                'description' => 'Pre-expedition briefing for the upcoming Mount Sinai documentation trip.',
                'starts_at' => $now->copy()->startOfMonth()->addDays(12)->setTime(19, 0),
                'ends_at' => $now->copy()->startOfMonth()->addDays(12)->setTime(21, 0),
                'all_day' => false,
                'location' => 'PTM Office',
                'color' => '#ef4444',
            ],
            [
                'title' => 'Hebrew Manuscript Transcription Session',
                'description' => 'Weekly transcription session for the Cochin Hebrew New Testament manuscripts.',
                'starts_at' => $now->copy()->startOfMonth()->addDays(3)->setTime(14, 0),
                'ends_at' => $now->copy()->startOfMonth()->addDays(3)->setTime(17, 0),
                'all_day' => false,
                'location' => 'Online / PTM Discord',
                'color' => '#3b82f6',
            ],
            [
                'title' => 'Biblical Archaeology Symposium',
                'description' => 'Annual symposium on biblical archaeology featuring Mount Sinai findings.',
                'starts_at' => $now->copy()->startOfMonth()->addDays(20)->setTime(9, 0),
                'ends_at' => $now->copy()->startOfMonth()->addDays(22)->setTime(17, 0),
                'all_day' => true,
                'location' => 'Conference Center, Dallas TX',
                'color' => '#8b5cf6',
            ],
            [
                'title' => 'Weekly Hebrew Study Group',
                'description' => 'Open study group for learning Biblical Hebrew with Jonathan Meyer.',
                'starts_at' => $now->copy()->startOfMonth()->addDays(7)->setTime(18, 0),
                'ends_at' => $now->copy()->startOfMonth()->addDays(7)->setTime(20, 0),
                'all_day' => false,
                'location' => 'Online',
                'color' => '#10b981',
            ],
            [
                'title' => 'Scroll of Mysteries Release Celebration',
                'description' => 'Celebration for the publication of the Cochin Hebrew Revelation translation.',
                'starts_at' => $now->copy()->startOfMonth()->addDays(15)->setTime(17, 0),
                'ends_at' => $now->copy()->startOfMonth()->addDays(15)->setTime(22, 0),
                'all_day' => false,
                'location' => 'PTM Office / Livestream',
                'color' => '#f97316',
            ],
            [
                'title' => 'Cochin Hebrew James Translation Review',
                'description' => 'Review session for the Cochin Hebrew James translation with Janice Baca.',
                'starts_at' => $now->copy()->startOfMonth()->addDays(10)->setTime(10, 0),
                'ends_at' => $now->copy()->startOfMonth()->addDays(10)->setTime(12, 0),
                'all_day' => false,
                'location' => 'PTM Office',
                'color' => '#ec4899',
            ],
            [
                'title' => 'Mount Sinai Expedition Departure',
                'description' => 'Departure for the Mount Sinai documentation expedition in Saudi Arabia.',
                'starts_at' => $now->copy()->startOfMonth()->addDays(25)->setTime(6, 0),
                'ends_at' => $now->copy()->startOfMonth()->addDays(35)->setTime(22, 0),
                'all_day' => true,
                'location' => 'Saudi Arabia',
                'color' => '#ef4444',
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
};
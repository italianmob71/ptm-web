<?php

namespace Database\Seeders;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
        [
            'title' => 'Cochin Manuscript Translation Workshop',
            'description' => 'Monthly workshop on translating the Cochin Hebrew manuscripts with Janice Baca and Bryan Williams.',
            'starts_at' => '2026-08-06 10:00:00',
            'ends_at' => '2026-08-06 14:00:00',
            'all_day' => 0,
            'location' => 'PTM Office / Online',
            'color' => '#f59e0b',
            'created_by' => null,
            'created_at' => '2026-08-11 01:02:58',
            'updated_at' => '2026-08-11 01:02:58',
            'deleted_at' => null,
        ],
        [
            'title' => 'Mount Sinai Expedition Briefing',
            'description' => 'Pre-expedition briefing for the upcoming Mount Sinai documentation trip.',
            'starts_at' => '2026-08-13 19:00:00',
            'ends_at' => '2026-08-13 21:00:00',
            'all_day' => 0,
            'location' => 'PTM Office',
            'color' => '#ef4444',
            'created_by' => null,
            'created_at' => '2026-08-11 01:02:58',
            'updated_at' => '2026-08-11 01:02:58',
            'deleted_at' => null,
        ],
        [
            'title' => 'Hebrew Manuscript Transcription Session',
            'description' => 'Weekly transcription session for the Cochin Hebrew New Testament manuscripts.',
            'starts_at' => '2026-08-04 14:00:00',
            'ends_at' => '2026-08-04 17:00:00',
            'all_day' => 0,
            'location' => 'Online / PTM Discord',
            'color' => '#3b82f6',
            'created_by' => null,
            'created_at' => '2026-08-11 01:02:58',
            'updated_at' => '2026-08-11 01:02:58',
            'deleted_at' => null,
        ],
        [
            'title' => 'Biblical Archaeology Symposium',
            'description' => 'Annual symposium on biblical archaeology featuring Mount Sinai findings.',
            'starts_at' => '2026-08-21 09:00:00',
            'ends_at' => '2026-08-23 17:00:00',
            'all_day' => 1,
            'location' => 'Conference Center, Dallas TX',
            'color' => '#8b5cf6',
            'created_by' => null,
            'created_at' => '2026-08-11 01:02:58',
            'updated_at' => '2026-08-11 01:02:58',
            'deleted_at' => null,
        ],
        [
            'title' => 'Weekly Hebrew Study Group',
            'description' => 'Open study group for learning Biblical Hebrew with Jonathan Meyer.',
            'starts_at' => '2026-08-08 18:00:00',
            'ends_at' => '2026-08-08 20:00:00',
            'all_day' => 0,
            'location' => 'Online',
            'color' => '#10b981',
            'created_by' => null,
            'created_at' => '2026-08-11 01:02:58',
            'updated_at' => '2026-08-11 01:02:58',
            'deleted_at' => null,
        ],
        [
            'title' => 'Scroll of Mysteries Release Celebration',
            'description' => 'Celebration for the publication of the Cochin Hebrew Revelation translation.',
            'starts_at' => '2026-08-16 17:00:00',
            'ends_at' => '2026-08-16 22:00:00',
            'all_day' => 0,
            'location' => 'PTM Office / Livestream',
            'color' => '#f97316',
            'created_by' => null,
            'created_at' => '2026-08-11 01:02:58',
            'updated_at' => '2026-08-11 01:02:58',
            'deleted_at' => null,
        ],
        [
            'title' => 'Cochin Hebrew James Translation Review',
            'description' => 'Review session for the Cochin Hebrew James translation with Janice Baca.',
            'starts_at' => '2026-08-11 10:00:00',
            'ends_at' => '2026-08-11 12:00:00',
            'all_day' => 0,
            'location' => 'PTM Office',
            'color' => '#ec4899',
            'created_by' => null,
            'created_at' => '2026-08-11 01:02:58',
            'updated_at' => '2026-08-11 01:02:58',
            'deleted_at' => null,
        ],
        [
            'title' => 'Mount Sinai Expedition Departure',
            'description' => 'Departure for the Mount Sinai documentation expedition in Saudi Arabia.',
            'starts_at' => '2026-08-26 06:00:00',
            'ends_at' => '2026-09-05 22:00:00',
            'all_day' => 1,
            'location' => 'Saudi Arabia',
            'color' => '#ef4444',
            'created_by' => null,
            'created_at' => '2026-08-11 01:02:58',
            'updated_at' => '2026-08-11 01:02:58',
            'deleted_at' => null,
        ]
        ];

        foreach ($events as $data) {
            Event::updateOrCreate(
                ['title' => $data['title'], 'starts_at' => $data['starts_at']],
                $data
            );
        }
    }
}

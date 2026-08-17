<?php

namespace Database\Seeders;

use App\Models\CochinChapter;
use Illuminate\Database\Seeder;

class CochinChapterSeeder extends Seeder
{
    public function run(): void
    {
        $chapters = [
        [
            'book_id' => 1,
            'chapter_number' => 1,
            'title' => 'Cochin Hebrew Matthew Chapter 1',
            'pdf_id' => 3,
            'video_id' => null,
            'published' => 1,
            'published_at' => '2026-08-14 17:34:16',
            'created_at' => '2026-08-14 17:34:16',
            'updated_at' => '2026-08-14 17:34:16',
        ],
        [
            'book_id' => 1,
            'chapter_number' => 2,
            'title' => null,
            'pdf_id' => 4,
            'video_id' => null,
            'published' => 1,
            'published_at' => '2026-08-14 23:27:48',
            'created_at' => '2026-08-14 23:27:48',
            'updated_at' => '2026-08-14 23:27:48',
        ]
        ];

        foreach ($chapters as $data) {
            CochinChapter::updateOrCreate(
                ['book_id' => $data['book_id'], 'chapter_number' => $data['chapter_number']],
                $data
            );
        }
    }
}

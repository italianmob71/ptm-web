<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Order matters:
     *   users → authors → books, blog_posts, articles (FK author_id)
     *   → images → pdfs → videos (standalone)
     *   → travel_notes (FK author_id, teaser_image_id→images)
     *   → cochin_books → cochin_chapters (FK book_id, pdf_id→pdfs)
     *   → events (standalone)
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AuthorSeeder::class,
            BookSeeder::class,
            BlogPostSeeder::class,
            ArticleSeeder::class,
            ImageSeeder::class,
            PdfSeeder::class,
            VideoSeeder::class,
            TravelNoteSeeder::class,
            CochinBookSeeder::class,
            CochinChapterSeeder::class,
            EventSeeder::class,
        ]);
    }
};

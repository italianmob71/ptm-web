<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Drop all unique slug indexes across every content table.
 * Slugs are NOT unique — the same slug may appear on multiple records.
 */
return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'articles',
            'blog_posts',
            'books',
            'cochin_books',
            'images',
            'pdfs',
            'travel_notes',
            'videos',
        ];

        foreach ($tables as $table) {
            $indexName = $table . '_slug_unique';

            // Check if the index exists, then drop it
            $exists = DB::select("
                SELECT COUNT(*) as cnt 
                FROM information_schema.STATISTICS 
                WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = ? 
                  AND INDEX_NAME = ?
            ", [$table, $indexName]);

            if (!empty($exists) && $exists[0]->cnt > 0) {
                Schema::table($table, function (Blueprint $blueprint) use ($indexName) {
                    $blueprint->dropIndex($indexName);
                });
            }
        }
    }

    public function down(): void
    {
        // No-op — re-adding unique indexes would fail if duplicates exist
    }
};

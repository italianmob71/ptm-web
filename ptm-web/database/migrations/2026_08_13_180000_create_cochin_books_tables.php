<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── cochin_books ──────────────────────────────────
        Schema::create('cochin_books', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->unique();
            $table->string('title', 255);
            $table->longText('description')->nullable();      // CKEditor — book intro / overview
            $table->longText('discoveries')->nullable();       // CKEditor — key findings tab
            $table->string('manuscript', 100)->nullable();     // e.g. "MS Oo.1.32"
            $table->enum('status', ['wip', 'complete'])->default('wip');
            $table->unsignedSmallInteger('total_chapters')->default(0);
            $table->foreignId('cover_image_id')->nullable()      // FK to images table
                  ->constrained('images')->nullOnDelete();
            $table->foreignId('complete_pdf_id')->nullable()     // FK to pdfs table — the unified book PDF
                  ->constrained('pdfs')->nullOnDelete();
            $table->boolean('published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── cochin_chapters ───────────────────────────────
        Schema::create('cochin_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')
                  ->constrained('cochin_books')->cascadeOnDelete();
            $table->unsignedSmallInteger('chapter_number');
            $table->string('title', 255)->nullable();         // e.g. "Chapter 1" or custom title
            $table->foreignId('pdf_id')->nullable()           // FK to pdfs table — chapter PDF
                  ->constrained('pdfs')->nullOnDelete();
            $table->foreignId('video_id')->nullable()         // FK to videos table — chapter video
                  ->constrained('videos')->nullOnDelete();
            $table->boolean('published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            // Unique: one chapter N per book
            $table->unique(['book_id', 'chapter_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cochin_chapters');
        Schema::dropIfExists('cochin_books');
    }
};

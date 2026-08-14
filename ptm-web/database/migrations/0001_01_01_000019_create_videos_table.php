<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('filename')->nullable();      // original file name (uploaded)
            $table->string('path')->nullable();           // relative path in public/videos/
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->nullable();       // e.g. "Cochin", "Travel Notes", "Teaching"
            $table->string('source_url')->nullable();      // YouTube/Rumble URL for embedded videos
            $table->string('source_platform')->nullable(); // 'youtube', 'rumble', 'local'
            $table->string('source_id')->nullable();       // video ID extracted from URL
            $table->bigInteger('file_size')->nullable();    // bytes (local files only)
            $table->string('mime_type')->nullable();
            $table->integer('duration')->nullable();        // seconds (if available)
            $table->string('thumbnail_path')->nullable();   // optional poster image
            $table->boolean('published')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['published']);
            $table->index(['category']);
            $table->index(['slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};

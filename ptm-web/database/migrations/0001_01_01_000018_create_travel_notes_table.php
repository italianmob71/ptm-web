<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('travel_notes', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 255)->unique();
            $table->string('title', 500);
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->json('photo_paths')->nullable();
            $table->string('biblical_reference', 255)->nullable();
            $table->string('location', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('sort_order');
            $table->index(['published', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_notes');
    }
};

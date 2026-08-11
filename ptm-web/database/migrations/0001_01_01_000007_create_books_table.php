<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('slug')->unique();
            $table->unsignedBigInteger('author_id');
            $table->text('body')->nullable();
            $table->string('isbn_13')->nullable();
            $table->string('isbn_10')->nullable();
            $table->string('amazon_link')->nullable();
            $table->string('lulu_link')->nullable();
            $table->string('image_front');
            $table->string('image_back')->nullable();
            $table->string('image_inner')->nullable();
            $table->string('edition')->nullable();
            $table->date('published_at')->nullable();
            $table->boolean('published')->default(false);
            $table->boolean('active')->default(true);
            $table->integer('page_count')->nullable();
            $table->string('language')->default('English');
            $table->decimal('price_usd', 8, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key
            $table->foreign('author_id')->references('id')->on('authors')->onDelete('cascade');

            // Indexes
            $table->index('slug');
            $table->index('author_id');
            $table->index(['published', 'active']);
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
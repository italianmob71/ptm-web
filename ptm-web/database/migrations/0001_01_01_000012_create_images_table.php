<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 255)->unique();
            $table->string('filename', 255);
            $table->string('path', 500);
            $table->string('alt_text', 500)->nullable();
            $table->string('caption', 500)->nullable();
            $table->string('mime_type', 100);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('category', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};

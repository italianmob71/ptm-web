<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdfs', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 255)->unique();
            $table->string('filename', 500);
            $table->string('path', 500);
            $table->string('title', 500)->nullable();
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->string('source_url', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdfs');
    }
};

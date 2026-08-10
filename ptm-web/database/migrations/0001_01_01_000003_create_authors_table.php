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
        Schema::create('authors', function (Blueprint $table) {
            $table->id(); // A_I id (bigint, unsigned, auto-increment, PK)
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('middle_initial', 5)->nullable();
            $table->text('bio')->nullable();
            $table->string('image', 255)->nullable(); // filename on disk
            $table->boolean('active')->default(true);
            $table->boolean('team_member')->default(false);
            $table->json('social_links')->nullable(); // facebook, youtube, rumble, linkedin, etc.
            $table->timestamps();
            $table->softDeletes();

            // Indexes for common lookups
            $table->index(['last_name', 'first_name']);
            $table->index('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
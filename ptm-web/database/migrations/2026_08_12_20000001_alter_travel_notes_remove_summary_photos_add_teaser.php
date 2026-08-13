<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('travel_notes', function (Blueprint $table) {
            $table->dropColumn(['summary', 'photo_paths']);
            $table->foreignId('teaser_image_id')->nullable()->after('content');
            $table->foreign('teaser_image_id')->references('id')->on('images')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('travel_notes', function (Blueprint $table) {
            $table->dropForeign(['teaser_image_id']);
            $table->dropColumn('teaser_image_id');
            $table->text('summary')->nullable()->after('title');
            $table->json('photo_paths')->nullable()->after('content');
        });
    }
};

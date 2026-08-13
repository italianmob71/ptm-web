<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('travel_notes', function (Blueprint $table) {
            $table->foreignId('author_id')->nullable()->after('id');
            $table->foreign('author_id')->references('id')->on('authors')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('travel_notes', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropColumn('author_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->string('facebook', 500)->nullable()->after('social_links');
            $table->string('youtube', 500)->nullable()->after('facebook');
            $table->string('rumble', 500)->nullable()->after('youtube');
            $table->string('linkedin', 500)->nullable()->after('rumble');
            $table->string('truthsocial', 500)->nullable()->after('linkedin');
        });
    }

    public function down(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->dropColumn(['facebook', 'youtube', 'rumble', 'linkedin', 'truthsocial']);
        });
    }
};

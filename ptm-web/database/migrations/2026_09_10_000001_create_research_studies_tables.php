<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('research_study_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content');
            $table->timestamps();
        });

        DB::table('research_study_pages')->insert([
            'id' => 1,
            'title' => 'Participate in a Research Study',
            'content' => <<<'HTML'
<p><a href="https://livingscroll.org">LivingScroll.org</a> offers a series of participatory studies inspired by the Cochin Hebrew New Testament manuscripts, exploring how ancient sacred texts can be received, practiced, and reflected on today. Each study invites people from diverse backgrounds to engage with Scripture through simple guided activities, personal reflection, and brief observations. Join us as we discover how these ancient manuscript traditions continue to speak, shape, and come alive in modern lives.</p>
HTML,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::create('research_studies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description');
            $table->dateTime('starts_at')->index();
            $table->dateTime('ends_at')->index();
            $table->string('application_url', 2048);
            $table->dateTime('application_cutoff_at');
            $table->string('image_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_studies');
        Schema::dropIfExists('research_study_pages');
    }
};

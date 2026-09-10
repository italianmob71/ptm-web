<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('special_study_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description');
            $table->timestamps();
        });
        DB::table('special_study_pages')->insert([
            'id' => 1,
            'title' => 'Special Studies',
            'description' => <<<'HTML'
<p>Special studies are everything Biblical, archeological and spiritually relevant for the building and edification of the body of Messiah. <a href="https://livingscroll.org">LivingScroll.org</a> is also dedicated to promoting work and articles of organizations that produce and publish translated manuscripts and findings, to include findings in the Dead Sea Scrolls.</p>
HTML,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        Schema::create('special_studies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pdf_id')->unique()->constrained('pdfs')->cascadeOnDelete();
            $table->unsignedInteger('display_order')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('special_studies');
        Schema::dropIfExists('special_study_pages');
    }
};

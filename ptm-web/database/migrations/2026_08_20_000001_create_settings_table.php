<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed initial settings
        \DB::table('settings')->insert([
            [
                'key'         => 'maintenance_mode',
                'value'       => '0',
                'description' => 'When enabled, the public site shows the maintenance page. Super-admins can still access the admin panel.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'key'         => 'site_name',
                'value'       => 'Project Truth Ministries',
                'description' => 'The display name of the site, used in page titles and meta tags.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'key'         => 'site_description',
                'value'       => '',
                'description' => 'Short description for meta tags and Open Graph.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'key'         => 'maintenance_message',
                'value'       => null,
                'description' => 'Custom message shown on the maintenance page (optional).',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'key'         => 'registration_enabled',
                'value'       => '0',
                'description' => 'Allow public self-registration. When disabled, accounts must be created by a super-admin.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
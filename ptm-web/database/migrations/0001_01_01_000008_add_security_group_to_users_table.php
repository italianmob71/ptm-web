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
        Schema::table('users', function (Blueprint $table) {
            // Security group: 0=public, 1=user, 2=contributor, 3=scholar, 4=power-user, 5=admin, 9=super-admin
            $table->unsignedSmallInteger('security_group')->default(1)->after('password');
            
            // Force password update on next login
            $table->boolean('force_update')->default(false)->after('security_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['security_group', 'force_update']);
        });
    }
};
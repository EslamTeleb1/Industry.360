<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->json('description')->nullable()->after('position');
            $table->json('social_links')->nullable()->after('description');
            $table->dropColumn('link');
        });
    }

    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn(['description', 'social_links']);
            $table->string('link')->nullable()->after('position');
        });
    }
};

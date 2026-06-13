<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('about_us_settings', function (Blueprint $table) {
            $table->json('sub_main_title')->nullable()->after('description');
            $table->json('sub_main_description')->nullable()->after('sub_main_title');
        });
    }

    public function down(): void
    {
        Schema::table('about_us_settings', function (Blueprint $table) {
            $table->dropColumn(['sub_main_title', 'sub_main_description']);
        });
    }
};

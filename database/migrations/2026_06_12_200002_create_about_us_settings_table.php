<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_us_settings', function (Blueprint $table) {
            $table->id();

            // Main section
            $table->json('title')->nullable();
            $table->json('description')->nullable();

            // Sub-about section
            $table->json('sub_title')->nullable();
            $table->json('sub_description')->nullable();

            // Percentage item 1
            $table->json('percentage_title_1')->nullable();
            $table->json('percentage_description_1')->nullable();
            $table->unsignedInteger('percentage_value_1')->nullable();

            // Percentage item 2
            $table->json('percentage_title_2')->nullable();
            $table->json('percentage_description_2')->nullable();
            $table->unsignedInteger('percentage_value_2')->nullable();

            // Percentage item 3
            $table->json('percentage_title_3')->nullable();
            $table->json('percentage_description_3')->nullable();
            $table->unsignedInteger('percentage_value_3')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_us_settings');
    }
};

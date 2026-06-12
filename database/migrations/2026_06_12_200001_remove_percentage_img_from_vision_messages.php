<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vision_messages', function (Blueprint $table) {
            $table->dropColumn([
                'img_path',
                'percentage_title_1',
                'percentage_value_1',
                'percentage_title_2',
                'percentage_value_2',
                'percentage_title_3',
                'percentage_value_3',
                'is_active',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('vision_messages', function (Blueprint $table) {
            $table->string('img_path')->nullable();
            $table->json('percentage_title_1')->nullable();
            $table->unsignedInteger('percentage_value_1')->nullable();
            $table->json('percentage_title_2')->nullable();
            $table->unsignedInteger('percentage_value_2')->nullable();
            $table->json('percentage_title_3')->nullable();
            $table->unsignedInteger('percentage_value_3')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }
};

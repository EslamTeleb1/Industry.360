<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('footer_items', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index(); // 'social' or 'image'
            $table->string('platform')->nullable();
            $table->string('label')->nullable();
            $table->string('url')->nullable();
            $table->string('image_path')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('footer_items');
    }
};

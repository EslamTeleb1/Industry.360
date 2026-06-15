<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomeSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('home_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_setting_id')->nullable()->constrained('home_settings')->onDelete('cascade');
            $table->json('sub_title')->nullable();
            $table->json('sub_description')->nullable();
            $table->json('percentage_title_1')->nullable();
            $table->json('percentage_description_1')->nullable();
            $table->integer('percentage_value_1')->default(0);
            $table->json('percentage_title_2')->nullable();
            $table->json('percentage_description_2')->nullable();
            $table->integer('percentage_value_2')->default(0);
            $table->json('percentage_title_3')->nullable();
            $table->json('percentage_description_3')->nullable();
            $table->integer('percentage_value_3')->default(0);
            $table->string('img')->nullable();
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
        Schema::dropIfExists('home_sections');
    }
}

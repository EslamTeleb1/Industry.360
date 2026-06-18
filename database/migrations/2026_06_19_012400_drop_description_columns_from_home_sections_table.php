<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDescriptionColumnsFromHomeSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('home_sections', function (Blueprint $table) {
            $table->dropColumn([
                'percentage_description_1',
                'percentage_description_2',
                'percentage_description_3',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('home_sections', function (Blueprint $table) {
            $table->json('percentage_description_1')->nullable()->after('percentage_title_1');
            $table->json('percentage_description_2')->nullable()->after('percentage_title_2');
            $table->json('percentage_description_3')->nullable()->after('percentage_title_3');
        });
    }
}

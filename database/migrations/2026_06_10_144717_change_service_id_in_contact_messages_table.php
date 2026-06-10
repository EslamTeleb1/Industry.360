<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            // Make foreign key columns nullable
            $table->unsignedBigInteger('industry_id')->nullable()->change();
            $table->unsignedBigInteger('service_id')->nullable()->change();
            $table->unsignedBigInteger('solution_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            // Revert to NOT NULL (assuming no null values exist)
            $table->unsignedBigInteger('industry_id')->nullable(false)->change();
            $table->unsignedBigInteger('service_id')->nullable(false)->change();
            $table->unsignedBigInteger('solution_id')->nullable(false)->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            // Drop existing foreign keys
            $table->dropForeign(['industry_id']);
            $table->dropForeign(['service_id']);
            $table->dropForeign(['solution_id']);
            $table->dropForeign(['package_id']);
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            // Add new foreign keys pointing to separate contact lookup tables
            $table->foreign('industry_id')
                ->references('id')
                ->on('contact_industries')
                ->cascadeOnDelete();

            $table->foreign('service_id')
                ->nullable()
                ->references('id')
                ->on('contact_services')
                ->nullOnDelete();

            $table->foreign('solution_id')
                ->nullable()
                ->references('id')
                ->on('contact_solutions')
                ->nullOnDelete();

            $table->foreign('package_id')
                ->nullable()
                ->references('id')
                ->on('packages')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropForeign(['industry_id']);
            $table->dropForeign(['service_id']);
            $table->dropForeign(['solution_id']);
            $table->dropForeign(['package_id']);
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            // Revert to old foreign keys (if services and packages tables still exist)
            $table->foreign('industry_id')
                ->references('id')
                ->on('services')
                ->cascadeOnDelete();

            $table->foreign('service_id')
                ->references('id')
                ->on('services')
                ->cascadeOnDelete();

            $table->foreign('solution_id')
                ->references('id')
                ->on('services')
                ->cascadeOnDelete();

            $table->foreign('package_id')
                ->references('id')
                ->on('packages')
                ->nullOnDelete();
        });
    }
};

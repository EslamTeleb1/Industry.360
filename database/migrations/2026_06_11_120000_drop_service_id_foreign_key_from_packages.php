<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the foreign key constraint on service_id since it now supports polymorphic relationships
        Schema::table('packages', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->foreign('service_id')->constrained('services')->cascadeOnDelete();
        });
    }
};

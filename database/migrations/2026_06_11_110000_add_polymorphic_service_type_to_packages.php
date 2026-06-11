<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the old migration's changes first if they exist
        if (Schema::hasColumn('packages', 'contact_type')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->dropIndex(['contact_type', 'contact_id']);
                $table->dropColumn(['contact_type', 'contact_id']);
            });
        }

        // Make service_id nullable
        Schema::table('packages', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id')->nullable()->change();
        });

        // Add service_type column for polymorphic relationships
        Schema::table('packages', function (Blueprint $table) {
            $table->string('service_type')->default('service')->after('service_id');
            // Options: 'service', 'contact_industry', 'contact_service', 'contact_solution'
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('service_type');
            $table->unsignedBigInteger('service_id')->nullable(false)->change();
        });
    }
};

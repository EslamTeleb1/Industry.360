<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // Add columns to support contact lookup packages
            $table->string('contact_type')->nullable()->after('service_id'); // 'industry', 'service', 'solution'
            $table->unsignedBigInteger('contact_id')->nullable()->after('contact_type');

            // Make service_id nullable since packages can now belong to either Service or Contact
            $table->unsignedBigInteger('service_id')->nullable()->change();

            // Add index for contact lookups
            $table->index(['contact_type', 'contact_id']);
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropIndex(['contact_type', 'contact_id']);
            $table->dropColumn(['contact_type', 'contact_id']);

            // Revert service_id to NOT NULL
            $table->unsignedBigInteger('service_id')->nullable(false)->change();
        });
    }
};

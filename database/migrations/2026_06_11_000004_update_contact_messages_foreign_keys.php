<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            // Drop existing foreign keys if present (ignore errors when not present)
            try { $table->dropForeign(['industry_id']); } catch (\Exception $e) {}
            try { $table->dropForeign(['service_id']); } catch (\Exception $e) {}
            try { $table->dropForeign(['solution_id']); } catch (\Exception $e) {}
            try { $table->dropForeign(['package_id']); } catch (\Exception $e) {}
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            // Add new foreign keys pointing to separate contact lookup tables
            $table->foreign('industry_id')
                ->references('id')
                ->on('contact_industries')
                ->cascadeOnDelete();

            $table->foreign('service_id')
                ->references('id')
                ->on('contact_services')
                ->nullOnDelete();

            $table->foreign('solution_id')
                ->references('id')
                ->on('contact_solutions')
                ->nullOnDelete();

            $table->foreign('package_id')
                ->references('id')
                ->on('packages')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            // Drop foreign keys if present when rolling back
            try { $table->dropForeign(['industry_id']); } catch (\Exception $e) {}
            try { $table->dropForeign(['service_id']); } catch (\Exception $e) {}
            try { $table->dropForeign(['solution_id']); } catch (\Exception $e) {}
            try { $table->dropForeign(['package_id']); } catch (\Exception $e) {}
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

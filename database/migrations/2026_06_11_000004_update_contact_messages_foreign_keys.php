<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Check if a foreign key exists on a given table.
     */
    protected function foreignKeyExists(string $table, string $foreignKeyName): bool
    {
        $schema = DB::connection()->getDatabaseName();
        $result = DB::selectOne("
            SELECT 1
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = ?
              AND TABLE_NAME = ?
              AND CONSTRAINT_TYPE = 'FOREIGN KEY'
              AND CONSTRAINT_NAME = ?
        ", [$schema, $table, $foreignKeyName]);

        return (bool) $result;
    }

    /**
     * Safely drop a foreign key if it exists.
     */
    protected function dropForeignKeyIfExists(string $table, string $foreignKeyName): void
    {
        if ($this->foreignKeyExists($table, $foreignKeyName)) {
            Schema::table($table, function (Blueprint $table) use ($foreignKeyName) {
                $table->dropForeign($foreignKeyName);
            });
        }
    }

    public function up(): void
    {
        // --- 1. Drop old foreign keys (if they still exist) ---
        $oldKeys = [
            'contact_messages_industry_id_foreign',
            'contact_messages_service_id_foreign',
            'contact_messages_solution_id_foreign',
            'contact_messages_package_id_foreign',
        ];

        foreach ($oldKeys as $key) {
            $this->dropForeignKeyIfExists('contact_messages', $key);
        }

        // --- 2. Clean up orphaned references (set to NULL) ---
        // Only run these updates if the target tables exist
        if (Schema::hasTable('contact_industries')) {
            DB::table('contact_messages')
                ->whereNotIn('industry_id', DB::table('contact_industries')->select('id'))
                ->update(['industry_id' => null]);
        }

        if (Schema::hasTable('contact_services')) {
            DB::table('contact_messages')
                ->whereNotIn('service_id', DB::table('contact_services')->select('id'))
                ->update(['service_id' => null]);
        }

        if (Schema::hasTable('contact_solutions')) {
            DB::table('contact_messages')
                ->whereNotIn('solution_id', DB::table('contact_solutions')->select('id'))
                ->update(['solution_id' => null]);
        }

        if (Schema::hasTable('packages')) {
            DB::table('contact_messages')
                ->whereNotIn('package_id', DB::table('packages')->select('id'))
                ->update(['package_id' => null]);
        }

        // --- 3. Add the new foreign keys ---
        Schema::table('contact_messages', function (Blueprint $table) {
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
        // Drop the new foreign keys first
        $newKeys = [
            'contact_messages_industry_id_foreign',
            'contact_messages_service_id_foreign',
            'contact_messages_solution_id_foreign',
            'contact_messages_package_id_foreign',
        ];

        foreach ($newKeys as $key) {
            $this->dropForeignKeyIfExists('contact_messages', $key);
        }

        // Revert to the old foreign keys (if old tables still exist)
        Schema::table('contact_messages', function (Blueprint $table) {
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
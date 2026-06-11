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
        // Define the expected default foreign key names
        $keys = [
            'contact_messages_industry_id_foreign',
            'contact_messages_service_id_foreign',
            'contact_messages_solution_id_foreign',
            'contact_messages_package_id_foreign',
        ];

        foreach ($keys as $key) {
            $this->dropForeignKeyIfExists('contact_messages', $key);
        }

        // Add the new foreign keys
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
        // Drop the *new* foreign keys first (they may or may not exist)
        $newKeys = [
            'contact_messages_industry_id_foreign',   // now points to contact_industries
            'contact_messages_service_id_foreign',    // now points to contact_services
            'contact_messages_solution_id_foreign',   // now points to contact_solutions
            'contact_messages_package_id_foreign',    // now points to packages
        ];

        foreach ($newKeys as $key) {
            $this->dropForeignKeyIfExists('contact_messages', $key);
        }

        // Revert to the old foreign keys (if their target tables still exist)
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
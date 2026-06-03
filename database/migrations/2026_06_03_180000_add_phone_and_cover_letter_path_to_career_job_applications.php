<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('career_job_applications', function (Blueprint $table) {
            $table->string('phone')->after('email');
            $table->string('cover_letter_path')->nullable()->after('cv_path');
        });

        // If cover_letter text column exists, drop it
        if (Schema::hasColumn('career_job_applications', 'cover_letter')) {
            Schema::table('career_job_applications', function (Blueprint $table) {
                $table->dropColumn('cover_letter');
            });
        }
    }

    public function down(): void
    {
        Schema::table('career_job_applications', function (Blueprint $table) {
            $table->dropColumn(['phone', 'cover_letter_path']);
            $table->text('cover_letter')->nullable()->after('cv_path');
        });
    }
};

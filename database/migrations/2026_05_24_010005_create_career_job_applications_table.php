<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('career_job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('career_job_id')->constrained('career_jobs')->cascadeOnDelete();
            $table->string('full_name');
            $table->string('email');
            $table->unsignedSmallInteger('years_of_experience');
            $table->date('start_date')->nullable();
            $table->decimal('expected_salary', 12, 2)->nullable();
            $table->string('linkedin_profile')->nullable();
            $table->string('cv_path');
            $table->text('cover_letter')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_job_applications');
    }
};

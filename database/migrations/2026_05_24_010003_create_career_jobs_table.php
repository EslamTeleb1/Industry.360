<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('career_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('career_departments')->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('career_locations')->cascadeOnDelete();
            $table->foreignId('job_type_id')->constrained('career_job_types')->cascadeOnDelete();
            $table->json('title');
            $table->json('description');
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_jobs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_packages', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('description')->nullable();
            $table->string('contact_type'); // 'industry', 'service', 'solution'
            $table->unsignedBigInteger('contact_id');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['contact_type', 'contact_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_packages');
    }
};

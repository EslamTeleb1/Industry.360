<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // To safely swap table names, we use a temporary name.
        // This preserves all data without dropping tables.
        Schema::rename('about_us_settings', 'temp_settings_swap');
        Schema::rename('home_settings', 'about_us_settings');
        Schema::rename('temp_settings_swap', 'home_settings');
    }

    public function down(): void
    {
        // Reverse the swap
        Schema::rename('about_us_settings', 'temp_settings_swap');
        Schema::rename('home_settings', 'about_us_settings');
        Schema::rename('temp_settings_swap', 'home_settings');
    }
};

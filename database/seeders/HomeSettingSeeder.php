<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HomeSetting;

class HomeSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HomeSetting::query()->firstOrCreate([], [
            'title' => [
                'en' => 'Welcome to Our Company',
                'ar' => 'مرحبًا بكم في شركتنا',
            ],
            'description' => [
                'en' => 'We build modern web and mobile solutions.',
                'ar' => 'نحن نبني حلول ويب وجوال حديثة.',
            ],
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AboutUsSetting;

class AboutUsSettingSeeder extends Seeder
{
    public function run(): void
    {
        $setting = AboutUsSetting::query()->firstOrCreate([]);

        $setting->title = [
            'en' => [
                ['text' => 'Know More ', 'style' => 'normal'],
                ['text' => 'About Us', 'style' => 'primary'],
                ['text' => ' Here', 'style' => 'normal'],
            ],
            'ar' => [
                ['text' => 'اعرف المزيد ', 'style' => 'normal'],
                ['text' => 'عنا', 'style' => 'primary'],
                ['text' => ' هنا', 'style' => 'normal'],
            ]
        ];

        if (! $setting->description) {
            $setting->description = [
                'en' => 'Default description text goes here.',
                'ar' => 'النص الافتراضي للوصف يوضع هنا.'
            ];
        }

        $setting->save();
    }
}

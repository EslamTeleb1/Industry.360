<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VisionMessageSetting;

class VisionMessageSettingSeeder extends Seeder
{
    public function run(): void
    {
        $setting = VisionMessageSetting::query()->firstOrCreate([]);

        $setting->title = [
            'en' => [
                ['text' => 'Our Core ', 'style' => 'normal'],
                ['text' => 'Vision', 'style' => 'primary'],
                ['text' => ' & Message', 'style' => 'normal'],
            ],
            'ar' => [
                ['text' => 'رؤيتنا ', 'style' => 'normal'],
                ['text' => 'ورسالتنا', 'style' => 'primary'],
                ['text' => ' الأساسية', 'style' => 'normal'],
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

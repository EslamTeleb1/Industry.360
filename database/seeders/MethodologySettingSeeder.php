<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MethodologySetting;

class MethodologySettingSeeder extends Seeder
{
    public function run(): void
    {
        $setting = MethodologySetting::query()->firstOrCreate([]);

        $setting->title = [
            'en' => [
                ['text' => 'Our Proven ', 'style' => 'normal'],
                ['text' => 'Methodology', 'style' => 'primary'],
                ['text' => ' Workflow', 'style' => 'normal'],
            ],
            'ar' => [
                ['text' => 'منهجيتنا ', 'style' => 'normal'],
                ['text' => 'المثبتة', 'style' => 'primary'],
                ['text' => ' في العمل', 'style' => 'normal'],
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

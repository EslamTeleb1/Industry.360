<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContactUsSetting;

class ContactUsSettingSeeder extends Seeder
{
    public function run(): void
    {
        $setting = ContactUsSetting::query()->firstOrCreate([]);

        $setting->title = [
            'en' => [
                ['text' => 'Get In ', 'style' => 'normal'],
                ['text' => 'Touch', 'style' => 'primary'],
                ['text' => ' With Us', 'style' => 'normal'],
            ],
            'ar' => [
                ['text' => 'ابق على ', 'style' => 'normal'],
                ['text' => 'تواصل', 'style' => 'primary'],
                ['text' => ' معنا', 'style' => 'normal'],
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

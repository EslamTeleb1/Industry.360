<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContactService;

class ContactServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'title' => ['en' => 'Web Development', 'ar' => 'تطوير الويب'],
                'description' => ['en' => 'Custom web application development', 'ar' => 'تطوير تطبيقات الويب المخصصة'],
                'img_path' => null,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Mobile Development', 'ar' => 'تطوير الجوال'],
                'description' => ['en' => 'iOS and Android app development', 'ar' => 'تطوير تطبيقات iOS و Android'],
                'img_path' => null,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Cloud Services', 'ar' => 'خدمات السحابة'],
                'description' => ['en' => 'Cloud infrastructure and deployment', 'ar' => 'البنية التحتية السحابية والنشر'],
                'img_path' => null,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Data Analytics', 'ar' => 'تحليل البيانات'],
                'description' => ['en' => 'Big data and analytics solutions', 'ar' => 'حلول البيانات الضخمة والتحليلات'],
                'img_path' => null,
                'order' => 4,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Cybersecurity', 'ar' => 'الأمن السيبراني'],
                'description' => ['en' => 'Security audit and compliance', 'ar' => 'تدقيق الأمان والامتثال'],
                'img_path' => null,
                'order' => 5,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'AI & Machine Learning', 'ar' => 'الذكاء الاصطناعي والتعلم الآلي'],
                'description' => ['en' => 'AI solutions and ML models', 'ar' => 'حلول الذكاء الاصطناعي ونماذج التعلم الآلي'],
                'img_path' => null,
                'order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($services as $data) {
            ContactService::create($data);
        }
    }
}

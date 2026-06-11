<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContactIndustry;

class ContactIndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $industries = [
            [
                'title' => ['en' => 'Technology', 'ar' => 'التكنولوجيا'],
                'description' => ['en' => 'Technology and IT solutions', 'ar' => 'حلول التكنولوجيا وتقنية المعلومات'],
                'img_path' => null,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Healthcare', 'ar' => 'الرعاية الصحية'],
                'description' => ['en' => 'Healthcare industry and medical services', 'ar' => 'صناعة الرعاية الصحية والخدمات الطبية'],
                'img_path' => null,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Finance', 'ar' => 'التمويل'],
                'description' => ['en' => 'Financial services and banking', 'ar' => 'الخدمات المالية والمصرفية'],
                'img_path' => null,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Retail', 'ar' => 'التجزئة'],
                'description' => ['en' => 'Retail and e-commerce businesses', 'ar' => 'متاجر التجزئة والتجارة الإلكترونية'],
                'img_path' => null,
                'order' => 4,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Manufacturing', 'ar' => 'التصنيع'],
                'description' => ['en' => 'Manufacturing and industrial sector', 'ar' => 'قطاع التصنيع والصناعة'],
                'img_path' => null,
                'order' => 5,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Education', 'ar' => 'التعليم'],
                'description' => ['en' => 'Education and training institutions', 'ar' => 'المؤسسات التعليمية والتدريب'],
                'img_path' => null,
                'order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($industries as $data) {
            ContactIndustry::create($data);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'type' => 'service',
                'title' => ['en' => 'Website Development', 'ar' => 'تطوير المواقع'],
                'description' => ['en' => 'Build modern websites', 'ar' => 'بناء مواقع حديثة'],
                'service_order' => 1,
            ],
            [
                'type' => 'service',
                'title' => ['en' => 'Mobile Apps', 'ar' => 'تطبيقات الجوال'],
                'description' => ['en' => 'Cross-platform mobile applications', 'ar' => 'تطبيقات جوال متعددة المنصات'],
                'service_order' => 2,
            ],
            [
                'type' => 'solution',
                'title' => ['en' => 'E-commerce Solution', 'ar' => 'حل التجارة الإلكترونية'],
                'description' => ['en' => 'Scalable e-commerce platforms', 'ar' => 'منصات تجارة إلكترونية قابلة للتوسع'],
                'service_order' => 1,
            ],
            [
                'type' => 'industry',
                'title' => ['en' => 'Healthcare', 'ar' => 'الرعاية الصحية'],
                'description' => ['en' => 'Healthcare industry solutions', 'ar' => 'حلول لصناعة الرعاية الصحية'],
                'service_order' => 1,
            ],
        ];

        foreach ($items as $data) {
            Service::create($data);
        }
    }
}

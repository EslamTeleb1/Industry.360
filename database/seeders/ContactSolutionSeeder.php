<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContactSolution;

class ContactSolutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $solutions = [
            [
                'title' => ['en' => 'Enterprise Resource Planning', 'ar' => 'تخطيط موارد المؤسسة'],
                'description' => ['en' => 'Integrated ERP system for business management', 'ar' => 'نظام ERP متكامل لإدارة الأعمال'],
                'img_path' => null,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Customer Relationship Management', 'ar' => 'إدارة علاقات العملاء'],
                'description' => ['en' => 'CRM platform for customer engagement', 'ar' => 'منصة CRM لتفاعل العملاء'],
                'img_path' => null,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'E-Commerce Platform', 'ar' => 'منصة التجارة الإلكترونية'],
                'description' => ['en' => 'Full-featured online store solution', 'ar' => 'حل متجر إلكتروني متكامل'],
                'img_path' => null,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Business Intelligence', 'ar' => 'ذكاء الأعمال'],
                'description' => ['en' => 'Advanced analytics and reporting tools', 'ar' => 'أدوات تحليلات ورعايات متقدمة'],
                'img_path' => null,
                'order' => 4,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Project Management Suite', 'ar' => 'مجموعة إدارة المشاريع'],
                'description' => ['en' => 'Comprehensive project management solution', 'ar' => 'حل إدارة المشاريع الشامل'],
                'img_path' => null,
                'order' => 5,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Digital Marketing Platform', 'ar' => 'منصة التسويق الرقمي'],
                'description' => ['en' => 'Integrated marketing automation tools', 'ar' => 'أدوات أتمتة التسويق المتكاملة'],
                'img_path' => null,
                'order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($solutions as $data) {
            ContactSolution::create($data);
        }
    }
}

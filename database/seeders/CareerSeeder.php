<?php

namespace Database\Seeders;

use App\Models\CareerDepartment;
use App\Models\CareerLocation;
use App\Models\CareerJobType;
use App\Models\CareerJob;
use App\Models\CareerJobRoleSection;
use App\Models\CareersPageSetting;
use Illuminate\Database\Seeder;

class CareerSeeder extends Seeder
{
    public function run(): void
    {
        // Departments
        $departments = [
            ['name' => ['en' => 'Human Resources', 'ar' => 'الموارد البشرية']],
            ['name' => ['en' => 'People & Culture', 'ar' => 'الأفراد والثقافة']],
        ];
        foreach ($departments as $dept) {
            CareerDepartment::firstOrCreate(['name->en' => $dept['name']['en']], $dept);
        }

        // Locations
        $locations = [
            ['name' => ['en' => 'Cairo', 'ar' => 'القاهرة']],
            ['name' => ['en' => 'Alexandria', 'ar' => 'الإسكندرية']],
        ];
        foreach ($locations as $loc) {
            CareerLocation::firstOrCreate(['name->en' => $loc['name']['en']], $loc);
        }

        // Job Types
        $jobTypes = [
            ['name' => ['en' => 'Full Time', 'ar' => 'دوام كامل']],
            ['name' => ['en' => 'Part Time', 'ar' => 'دوام جزئي']],
        ];
        foreach ($jobTypes as $type) {
            CareerJobType::firstOrCreate(['name->en' => $type['name']['en']], $type);
        }

        // Careers Page Setting
        CareersPageSetting::firstOrCreate([], [
            'description' => ['en' => 'Join our team', 'ar' => 'انضم إلى فريقنا'],
            'banner_image_path' => null,
        ]);

        // Example Job
        $job = CareerJob::firstOrCreate([
            'title->en' => 'Software Engineer',
        ], [
            'department_id' => CareerDepartment::first()->id,
            'location_id' => CareerLocation::first()->id,
            'job_type_id' => CareerJobType::first()->id,
            'title' => ['en' => 'Software Engineer', 'ar' => 'مهندس برمجيات'],
            'description' => ['en' => 'Develop apps', 'ar' => 'تطوير تطبيقات'],
            'image_path' => null,
            'is_active' => true,
        ]);

        CareerJobRoleSection::firstOrCreate([
            'career_job_id' => $job->id,
            'title->en' => 'Responsibilities',
        ], [
            'career_job_id' => $job->id,
            'title' => ['en' => 'Responsibilities', 'ar' => 'المسؤوليات'],
            'description' => ['en' => 'Do X', 'ar' => 'فعل X'],
            'sort_order' => 0,
        ]);
    }
}

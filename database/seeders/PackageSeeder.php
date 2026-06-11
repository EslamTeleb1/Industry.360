<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\ContactIndustry;
use App\Models\ContactService;
use App\Models\ContactSolution;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        // Packages for Contact Industries
        $industries = ContactIndustry::all();
        foreach ($industries as $industry) {
            $industryTitles = $industry->getTranslations('title');
            $industryDescriptions = $industry->getTranslations('description');

            Package::create([
                'service_id' => $industry->id,
                'service_type' => 'contact_industry',
                'title' => [
                    'en' => "{$industryTitles['en']} Package",
                    'ar' => "{$industryTitles['ar']} حزمة",
                ],
                'description' => [
                    'en' => "Package for {$industryTitles['en']} industry lookup",
                    'ar' => "حزمة لبحث صناعة {$industryTitles['ar']}",
                ],
                'is_active' => true,
            ]);
        }

        // Packages for Contact Services
        $services = ContactService::all();
        foreach ($services as $service) {
            $serviceTitles = $service->getTranslations('title');
            $serviceDescriptions = $service->getTranslations('description');

            Package::create([
                'service_id' => $service->id,
                'service_type' => 'contact_service',
                'title' => [
                    'en' => "{$serviceTitles['en']} Package",
                    'ar' => "{$serviceTitles['ar']} حزمة",
                ],
                'description' => [
                    'en' => "Package for {$serviceTitles['en']} service lookup",
                    'ar' => "حزمة لبحث خدمة {$serviceTitles['ar']}",
                ],
                'is_active' => true,
            ]);
        }

        // Packages for Contact Solutions
        $solutions = ContactSolution::all();
        foreach ($solutions as $solution) {
            $solutionTitles = $solution->getTranslations('title');
            $solutionDescriptions = $solution->getTranslations('description');

            Package::create([
                'service_id' => $solution->id,
                'service_type' => 'contact_solution',
                'title' => [
                    'en' => "{$solutionTitles['en']} Package",
                    'ar' => "{$solutionTitles['ar']} حزمة",
                ],
                'description' => [
                    'en' => "Package for {$solutionTitles['en']} solution lookup",
                    'ar' => "حزمة لبحث حل {$solutionTitles['ar']}",
                ],
                'is_active' => true,
            ]);
        }
    }
}

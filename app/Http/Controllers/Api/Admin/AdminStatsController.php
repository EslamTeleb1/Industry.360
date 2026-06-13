<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Service;
use App\Models\CareerJob;
use App\Models\CareerJobApplication;
use App\Models\ContactMessage;
use App\Models\TeamMember;
use App\Models\Package;
use App\Models\Blog;
use App\Models\CaseStudy;
use App\Models\Partner;
use App\Models\Faq;

class AdminStatsController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $stats = [
            'services_count' => Service::where('type', 'service')->count(),
            'solutions_count' => Service::where('type', 'solution')->count(),
            'industries_count' => Service::where('type', 'industry')->count(),
            'careers_count' => CareerJob::count(),
            'job_applications_count' => CareerJobApplication::count(),
            'home_messages_count' => ContactMessage::count(),
            'team_members_count' => TeamMember::count(),
            'blogs_count' => Blog::count(),
            'case_studies_count' => CaseStudy::count(),
            'partners_count' => Partner::count(),
            'faqs_count' => Faq::count(),
            'packages_count' => Package::count(),

            // Useful additional stats
            'active_services_count' => Service::where('type', 'service')->where('is_active', true)->count(),
            'active_solutions_count' => Service::where('type', 'solution')->where('is_active', true)->count(),
            'active_careers_count' => CareerJob::where('is_active', true)->count(),
            'recent_home_messages' => ContactMessage::where('created_at', '>=', now()->subDays(7))->count(),
            'recent_job_applications' => CareerJobApplication::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return $this->successResponse([
            'stats' => $stats
        ], 'Admin stats retrieved successfully');
    }
}

<?php

use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\AdminRolePermissionController;
use App\Http\Controllers\Api\Admin\CareerDepartmentController;
use App\Http\Controllers\Api\Admin\CareerJobController;
use App\Http\Controllers\Api\Admin\CareerJobTypeController;
use App\Http\Controllers\Api\Admin\CareerLocationController;
use App\Http\Controllers\Api\Admin\CareerPageController;
use App\Http\Controllers\Api\Public\CareerApplicationController;
use App\Http\Controllers\Api\Public\CareerPublicController;
use Illuminate\Support\Facades\Route;

Route::post('admin/login', [AdminAuthController::class, 'login']);

Route::get('careers/page', [CareerPublicController::class, 'page']);
Route::get('careers/lookups', [CareerPublicController::class, 'lookups']);
Route::get('careers/jobs', [CareerPublicController::class, 'index']);
Route::get('careers/jobs/{job}', [CareerPublicController::class, 'show']);
Route::post('careers/jobs/{job}/apply', [CareerApplicationController::class, 'apply']);

Route::get('services', [\App\Http\Controllers\Api\Public\ServiceController::class, 'index']);
Route::get('services/{item}', [\App\Http\Controllers\Api\Public\ServiceController::class, 'show']);
Route::get('solutions', [\App\Http\Controllers\Api\Public\SolutionController::class, 'index']);
Route::get('solutions/{item}', [\App\Http\Controllers\Api\Public\SolutionController::class, 'show']);
Route::get('industries', [\App\Http\Controllers\Api\Public\IndustryController::class, 'index']);
Route::get('industries/{item}', [\App\Http\Controllers\Api\Public\IndustryController::class, 'show']);

Route::get('contact/services', [\App\Http\Controllers\Api\Public\ContactLookupPublicController::class, 'services']);
Route::get('contact/services/{item}', [\App\Http\Controllers\Api\Public\ContactLookupPublicController::class, 'showService']);
Route::get('contact/solutions', [\App\Http\Controllers\Api\Public\ContactLookupPublicController::class, 'solutions']);
Route::get('contact/solutions/{item}', [\App\Http\Controllers\Api\Public\ContactLookupPublicController::class, 'showSolution']);
Route::get('contact/industries', [\App\Http\Controllers\Api\Public\ContactLookupPublicController::class, 'industries']);
Route::get('contact/industries/{item}', [\App\Http\Controllers\Api\Public\ContactLookupPublicController::class, 'showIndustry']);

Route::get('contact/lookups', [\App\Http\Controllers\Api\Public\ContactController::class, 'lookups']);
Route::post('contact', [\App\Http\Controllers\Api\Public\ContactController::class, 'store']);
Route::get('contact-us/setting', [\App\Http\Controllers\Api\Public\ContactUsSettingController::class, 'show']);

Route::get('blogs/categories', [\App\Http\Controllers\Api\Public\BlogController::class, 'categories']);
Route::get('blogs', [\App\Http\Controllers\Api\Public\BlogController::class, 'index']);
Route::get('blogs/{blog}', [\App\Http\Controllers\Api\Public\BlogController::class, 'show']);

Route::get('faqs/categories', [\App\Http\Controllers\Api\Public\FaqController::class, 'categories']);
Route::get('faqs', [\App\Http\Controllers\Api\Public\FaqController::class, 'index']);
Route::get('partners', [\App\Http\Controllers\Api\Public\PartnerController::class, 'index']);
Route::get('case-studies', [\App\Http\Controllers\Api\Public\CaseStudyController::class, 'index']);
Route::get('case-studies/{caseStudy}', [\App\Http\Controllers\Api\Public\CaseStudyController::class, 'show']);

Route::get('vision-message/setting', [\App\Http\Controllers\Api\Public\VisionMessageController::class, 'setting']);
Route::get('vision-messages', [\App\Http\Controllers\Api\Public\VisionMessageController::class, 'index']);

Route::get('methodology/setting', [\App\Http\Controllers\Api\Public\MethodologyController::class, 'setting']);
Route::get('methodologies', [\App\Http\Controllers\Api\Public\MethodologyController::class, 'index']);

Route::get('team/setting', [\App\Http\Controllers\Api\Public\TeamController::class, 'setting']);
Route::get('team-members', [\App\Http\Controllers\Api\Public\TeamController::class, 'index']);
Route::get('team-members/{teamMember}', [\App\Http\Controllers\Api\Public\TeamController::class, 'show']);

Route::get('home/setting', [\App\Http\Controllers\Api\Public\HomeSettingController::class, 'show']);
Route::get('about-us/setting', [\App\Http\Controllers\Api\Public\AboutUsSettingController::class, 'show']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('admin/logout', [AdminAuthController::class, 'logout']);
    Route::get('admin/me', [AdminAuthController::class, 'me']);
    Route::get('admin/stats', [\App\Http\Controllers\Api\Admin\AdminStatsController::class, 'index']);

    Route::get('admin/roles', [AdminRolePermissionController::class, 'roles']);
    Route::get('admin/permissions', [AdminRolePermissionController::class, 'permissions']);
    Route::post('admin/users/{user}/roles', [AdminRolePermissionController::class, 'assignRoles']);
    Route::post('admin/users/{user}/permissions', [AdminRolePermissionController::class, 'assignPermissions']);

    Route::get('admin/careers/departments', [CareerDepartmentController::class, 'index']);
    Route::post('admin/careers/departments', [CareerDepartmentController::class, 'store']);
    Route::post('admin/careers/departments/{department}', [CareerDepartmentController::class, 'update']);
    Route::delete('admin/careers/departments/{department}', [CareerDepartmentController::class, 'destroy']);

    Route::get('admin/careers/locations', [CareerLocationController::class, 'index']);
    Route::post('admin/careers/locations', [CareerLocationController::class, 'store']);
    Route::post('admin/careers/locations/{location}', [CareerLocationController::class, 'update']);
    Route::delete('admin/careers/locations/{location}', [CareerLocationController::class, 'destroy']);

    Route::get('admin/careers/job-types', [CareerJobTypeController::class, 'index']);
    Route::post('admin/careers/job-types', [CareerJobTypeController::class, 'store']);
    Route::post('admin/careers/job-types/{jobType}', [CareerJobTypeController::class, 'update']);
    Route::delete('admin/careers/job-types/{jobType}', [CareerJobTypeController::class, 'destroy']);

    Route::get('admin/careers/jobs', [CareerJobController::class, 'index']);
    Route::post('admin/careers/jobs', [CareerJobController::class, 'store']);
    Route::get('admin/careers/jobs/{job}', [CareerJobController::class, 'show']);
    Route::post('admin/careers/jobs/{job}', [CareerJobController::class, 'update']);
    Route::delete('admin/careers/jobs/{job}', [CareerJobController::class, 'destroy']);

    // List all applicants for admin
    Route::get('admin/careers/applications', [\App\Http\Controllers\Api\Admin\CareerJobApplicationController::class, 'index']);

    Route::get('admin/contact-messages', [\App\Http\Controllers\Api\Admin\ContactMessageController::class, 'index']);
    Route::get('admin/contact-messages/{contactMessage}', [\App\Http\Controllers\Api\Admin\ContactMessageController::class, 'show']);

    Route::get('admin/careers/page', [CareerPageController::class, 'show']);
    Route::post('admin/careers/page', [CareerPageController::class, 'update']);

    Route::get('admin/services', [\App\Http\Controllers\Api\Admin\AdminServiceController::class, 'index']);
    Route::post('admin/services', [\App\Http\Controllers\Api\Admin\AdminServiceController::class, 'store']);
    Route::get('admin/services/{item}', [\App\Http\Controllers\Api\Admin\AdminServiceController::class, 'show']);
    Route::post('admin/services/{item}', [\App\Http\Controllers\Api\Admin\AdminServiceController::class, 'update']);
    Route::delete('admin/services/{item}', [\App\Http\Controllers\Api\Admin\AdminServiceController::class, 'destroy']);

    Route::get('admin/solutions', [\App\Http\Controllers\Api\Admin\AdminSolutionController::class, 'index']);
    Route::post('admin/solutions', [\App\Http\Controllers\Api\Admin\AdminSolutionController::class, 'store']);
    Route::get('admin/solutions/{item}', [\App\Http\Controllers\Api\Admin\AdminSolutionController::class, 'show']);
    Route::post('admin/solutions/{item}', [\App\Http\Controllers\Api\Admin\AdminSolutionController::class, 'update']);
    Route::delete('admin/solutions/{item}', [\App\Http\Controllers\Api\Admin\AdminSolutionController::class, 'destroy']);

    Route::get('admin/industries', [\App\Http\Controllers\Api\Admin\AdminIndustryController::class, 'index']);
    Route::post('admin/industries', [\App\Http\Controllers\Api\Admin\AdminIndustryController::class, 'store']);
    Route::get('admin/industries/{item}', [\App\Http\Controllers\Api\Admin\AdminIndustryController::class, 'show']);
    Route::post('admin/industries/{item}', [\App\Http\Controllers\Api\Admin\AdminIndustryController::class, 'update']);
    Route::delete('admin/industries/{item}', [\App\Http\Controllers\Api\Admin\AdminIndustryController::class, 'destroy']);

    Route::get('admin/blog-categories', [\App\Http\Controllers\Api\Admin\AdminBlogCategoryController::class, 'index']);
    Route::post('admin/blog-categories', [\App\Http\Controllers\Api\Admin\AdminBlogCategoryController::class, 'store']);
    Route::get('admin/blog-categories/{blogCategory}', [\App\Http\Controllers\Api\Admin\AdminBlogCategoryController::class, 'show']);
    Route::post('admin/blog-categories/{blogCategory}', [\App\Http\Controllers\Api\Admin\AdminBlogCategoryController::class, 'update']);
    Route::delete('admin/blog-categories/{blogCategory}', [\App\Http\Controllers\Api\Admin\AdminBlogCategoryController::class, 'destroy']);

    Route::get('admin/blogs', [\App\Http\Controllers\Api\Admin\AdminBlogController::class, 'index']);
    Route::post('admin/blogs', [\App\Http\Controllers\Api\Admin\AdminBlogController::class, 'store']);
    Route::get('admin/blogs/{blog}', [\App\Http\Controllers\Api\Admin\AdminBlogController::class, 'show']);
    Route::post('admin/blogs/{blog}', [\App\Http\Controllers\Api\Admin\AdminBlogController::class, 'update']);
    Route::delete('admin/blogs/{blog}', [\App\Http\Controllers\Api\Admin\AdminBlogController::class, 'destroy']);

    Route::get('admin/faq-categories', [\App\Http\Controllers\Api\Admin\AdminFaqCategoryController::class, 'index']);
    Route::post('admin/faq-categories', [\App\Http\Controllers\Api\Admin\AdminFaqCategoryController::class, 'store']);
    Route::get('admin/faq-categories/{faqCategory}', [\App\Http\Controllers\Api\Admin\AdminFaqCategoryController::class, 'show']);
    Route::post('admin/faq-categories/{faqCategory}', [\App\Http\Controllers\Api\Admin\AdminFaqCategoryController::class, 'update']);
    Route::delete('admin/faq-categories/{faqCategory}', [\App\Http\Controllers\Api\Admin\AdminFaqCategoryController::class, 'destroy']);

    Route::get('admin/faqs', [\App\Http\Controllers\Api\Admin\AdminFaqController::class, 'index']);
    Route::post('admin/faqs', [\App\Http\Controllers\Api\Admin\AdminFaqController::class, 'store']);
    Route::get('admin/faqs/{faq}', [\App\Http\Controllers\Api\Admin\AdminFaqController::class, 'show']);
    Route::post('admin/faqs/{faq}', [\App\Http\Controllers\Api\Admin\AdminFaqController::class, 'update']);
    Route::delete('admin/faqs/{faq}', [\App\Http\Controllers\Api\Admin\AdminFaqController::class, 'destroy']);

    Route::get('admin/partners', [\App\Http\Controllers\Api\Admin\AdminPartnerController::class, 'index']);
    Route::post('admin/partners', [\App\Http\Controllers\Api\Admin\AdminPartnerController::class, 'store']);
    Route::get('admin/partners/{partner}', [\App\Http\Controllers\Api\Admin\AdminPartnerController::class, 'show']);
    Route::post('admin/partners/{partner}', [\App\Http\Controllers\Api\Admin\AdminPartnerController::class, 'update']);
    Route::delete('admin/partners/{partner}', [\App\Http\Controllers\Api\Admin\AdminPartnerController::class, 'destroy']);

    Route::get('admin/case-studies', [\App\Http\Controllers\Api\Admin\AdminCaseStudyController::class, 'index']);
    Route::post('admin/case-studies', [\App\Http\Controllers\Api\Admin\AdminCaseStudyController::class, 'store']);
    Route::get('admin/case-studies/{caseStudy}', [\App\Http\Controllers\Api\Admin\AdminCaseStudyController::class, 'show']);
    Route::post('admin/case-studies/{caseStudy}', [\App\Http\Controllers\Api\Admin\AdminCaseStudyController::class, 'update']);
    Route::delete('admin/case-studies/{caseStudy}', [\App\Http\Controllers\Api\Admin\AdminCaseStudyController::class, 'destroy']);

    // Contact Lookups Admin Routes
    Route::get('admin/contact-industries', [\App\Http\Controllers\Api\Admin\AdminContactIndustryController::class, 'index']);
    Route::post('admin/contact-industries', [\App\Http\Controllers\Api\Admin\AdminContactIndustryController::class, 'store']);
    Route::get('admin/contact-industries/{industry}', [\App\Http\Controllers\Api\Admin\AdminContactIndustryController::class, 'show']);
    Route::post('admin/contact-industries/{industry}', [\App\Http\Controllers\Api\Admin\AdminContactIndustryController::class, 'update']);
    Route::delete('admin/contact-industries/{industry}', [\App\Http\Controllers\Api\Admin\AdminContactIndustryController::class, 'destroy']);

    Route::get('admin/contact-services', [\App\Http\Controllers\Api\Admin\AdminContactServiceController::class, 'index']);
    Route::post('admin/contact-services', [\App\Http\Controllers\Api\Admin\AdminContactServiceController::class, 'store']);
    Route::get('admin/contact-services/{service}', [\App\Http\Controllers\Api\Admin\AdminContactServiceController::class, 'show']);
    Route::post('admin/contact-services/{service}', [\App\Http\Controllers\Api\Admin\AdminContactServiceController::class, 'update']);
    Route::delete('admin/contact-services/{service}', [\App\Http\Controllers\Api\Admin\AdminContactServiceController::class, 'destroy']);

    Route::get('admin/contact-solutions', [\App\Http\Controllers\Api\Admin\AdminContactSolutionController::class, 'index']);
    Route::post('admin/contact-solutions', [\App\Http\Controllers\Api\Admin\AdminContactSolutionController::class, 'store']);
    Route::get('admin/contact-solutions/{solution}', [\App\Http\Controllers\Api\Admin\AdminContactSolutionController::class, 'show']);
    Route::post('admin/contact-solutions/{solution}', [\App\Http\Controllers\Api\Admin\AdminContactSolutionController::class, 'update']);
    Route::delete('admin/contact-solutions/{solution}', [\App\Http\Controllers\Api\Admin\AdminContactSolutionController::class, 'destroy']);

    Route::apiResource('admin/packages', \App\Http\Controllers\Api\Admin\AdminPackageController::class);

    // Vision & Message
    Route::get('admin/vision-message/setting', [\App\Http\Controllers\Api\Admin\AdminVisionMessageSettingController::class, 'show']);
    Route::post('admin/vision-message/setting', [\App\Http\Controllers\Api\Admin\AdminVisionMessageSettingController::class, 'update']);

    Route::get('admin/vision-messages', [\App\Http\Controllers\Api\Admin\AdminVisionMessageController::class, 'index']);
    Route::post('admin/vision-messages', [\App\Http\Controllers\Api\Admin\AdminVisionMessageController::class, 'store']);
    Route::get('admin/vision-messages/{visionMessage}', [\App\Http\Controllers\Api\Admin\AdminVisionMessageController::class, 'show']);
    Route::post('admin/vision-messages/{visionMessage}', [\App\Http\Controllers\Api\Admin\AdminVisionMessageController::class, 'update']);
    Route::delete('admin/vision-messages/{visionMessage}', [\App\Http\Controllers\Api\Admin\AdminVisionMessageController::class, 'destroy']);

    // Our Methodology
    Route::get('admin/methodology/setting', [\App\Http\Controllers\Api\Admin\AdminMethodologySettingController::class, 'show']);
    Route::post('admin/methodology/setting', [\App\Http\Controllers\Api\Admin\AdminMethodologySettingController::class, 'update']);

    Route::get('admin/methodologies', [\App\Http\Controllers\Api\Admin\AdminMethodologyController::class, 'index']);
    Route::post('admin/methodologies', [\App\Http\Controllers\Api\Admin\AdminMethodologyController::class, 'store']);
    Route::get('admin/methodologies/{methodology}', [\App\Http\Controllers\Api\Admin\AdminMethodologyController::class, 'show']);
    Route::post('admin/methodologies/{methodology}', [\App\Http\Controllers\Api\Admin\AdminMethodologyController::class, 'update']);
    Route::delete('admin/methodologies/{methodology}', [\App\Http\Controllers\Api\Admin\AdminMethodologyController::class, 'destroy']);

    // Our Team
    Route::get('admin/team/setting', [\App\Http\Controllers\Api\Admin\AdminTeamSettingController::class, 'show']);
    Route::post('admin/team/setting', [\App\Http\Controllers\Api\Admin\AdminTeamSettingController::class, 'update']);

    Route::get('admin/team-members', [\App\Http\Controllers\Api\Admin\AdminTeamMemberController::class, 'index']);
    Route::post('admin/team-members', [\App\Http\Controllers\Api\Admin\AdminTeamMemberController::class, 'store']);
    Route::get('admin/team-members/{teamMember}', [\App\Http\Controllers\Api\Admin\AdminTeamMemberController::class, 'show']);
    Route::post('admin/team-members/{teamMember}', [\App\Http\Controllers\Api\Admin\AdminTeamMemberController::class, 'update']);
    Route::delete('admin/team-members/{teamMember}', [\App\Http\Controllers\Api\Admin\AdminTeamMemberController::class, 'destroy']);

    // About Us
    Route::get('admin/about-us/setting', [\App\Http\Controllers\Api\Admin\AdminAboutUsSettingController::class, 'show']);
    Route::post('admin/about-us/setting', [\App\Http\Controllers\Api\Admin\AdminAboutUsSettingController::class, 'update']);

    // Home Setting
    Route::get('admin/home/setting', [\App\Http\Controllers\Api\Admin\AdminHomeSettingController::class, 'show']);
    Route::post('admin/home/setting', [\App\Http\Controllers\Api\Admin\AdminHomeSettingController::class, 'update']);

    // Contact Us Setting
    Route::get('admin/contact-us/setting', [\App\Http\Controllers\Api\Admin\AdminContactUsSettingController::class, 'show']);
    Route::post('admin/contact-us/setting', [\App\Http\Controllers\Api\Admin\AdminContactUsSettingController::class, 'update']);
});

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

Route::middleware('auth:sanctum')->group(function () {
    Route::post('admin/logout', [AdminAuthController::class, 'logout']);
    Route::get('admin/me', [AdminAuthController::class, 'me']);

    Route::get('admin/roles', [AdminRolePermissionController::class, 'roles']);
    Route::get('admin/permissions', [AdminRolePermissionController::class, 'permissions']);
    Route::post('admin/users/{user}/roles', [AdminRolePermissionController::class, 'assignRoles']);
    Route::post('admin/users/{user}/permissions', [AdminRolePermissionController::class, 'assignPermissions']);

    Route::get('admin/careers/departments', [CareerDepartmentController::class, 'index']);
    Route::post('admin/careers/departments', [CareerDepartmentController::class, 'store']);
    Route::put('admin/careers/departments/{department}', [CareerDepartmentController::class, 'update']);
    Route::delete('admin/careers/departments/{department}', [CareerDepartmentController::class, 'destroy']);

    Route::get('admin/careers/locations', [CareerLocationController::class, 'index']);
    Route::post('admin/careers/locations', [CareerLocationController::class, 'store']);
    Route::put('admin/careers/locations/{location}', [CareerLocationController::class, 'update']);
    Route::delete('admin/careers/locations/{location}', [CareerLocationController::class, 'destroy']);

    Route::get('admin/careers/job-types', [CareerJobTypeController::class, 'index']);
    Route::post('admin/careers/job-types', [CareerJobTypeController::class, 'store']);
    Route::put('admin/careers/job-types/{jobType}', [CareerJobTypeController::class, 'update']);
    Route::delete('admin/careers/job-types/{jobType}', [CareerJobTypeController::class, 'destroy']);

    Route::get('admin/careers/jobs', [CareerJobController::class, 'index']);
    Route::post('admin/careers/jobs', [CareerJobController::class, 'store']);
    Route::get('admin/careers/jobs/{job}', [CareerJobController::class, 'show']);
    Route::put('admin/careers/jobs/{job}', [CareerJobController::class, 'update']);
    Route::delete('admin/careers/jobs/{job}', [CareerJobController::class, 'destroy']);

    // List all applicants for admin
    Route::get('admin/careers/applications', [\App\Http\Controllers\Api\Admin\CareerJobApplicationController::class, 'index']);

    Route::get('admin/careers/page', [CareerPageController::class, 'show']);
    Route::post('admin/careers/page', [CareerPageController::class, 'update']);
});

<?php

use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\AdminRolePermissionController;
use Illuminate\Support\Facades\Route;

Route::post('admin/login', [AdminAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('admin/logout', [AdminAuthController::class, 'logout']);
    Route::get('admin/me', [AdminAuthController::class, 'me']);

    Route::get('admin/roles', [AdminRolePermissionController::class, 'roles']);
    Route::get('admin/permissions', [AdminRolePermissionController::class, 'permissions']);
    Route::post('admin/users/{user}/roles', [AdminRolePermissionController::class, 'assignRoles']);
    Route::post('admin/users/{user}/permissions', [AdminRolePermissionController::class, 'assignPermissions']);
});

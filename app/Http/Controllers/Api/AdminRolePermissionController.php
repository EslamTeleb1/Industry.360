<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminRolePermissionController extends Controller
{
    use ApiResponse;
    public function roles()
    {
        return $this->successResponse([
            'roles' => Role::query()->orderBy('name')->get(),
        ], 'Roles retrieved successfully');
    }

    public function permissions()
    {
        return $this->successResponse([
            'permissions' => Permission::query()->orderBy('name')->get(),
        ], 'Permissions retrieved successfully');
    }

    public function assignRoles(Request $request, User $user)
    {
        $data = $request->validate([
            'roles' => ['required', 'array'],
            'roles.*' => ['string'],
        ]);

        $user->syncRoles($data['roles']);

        return $this->successResponse([
            'user' => $user->load('roles', 'permissions'),
        ], 'Roles assigned successfully');
    }

    public function assignPermissions(Request $request, User $user)
    {
        $data = $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*' => ['string'],
        ]);

        $user->syncPermissions($data['permissions']);

        return $this->successResponse([
            'user' => $user->load('roles', 'permissions'),
        ], 'Permissions assigned successfully');
    }
}

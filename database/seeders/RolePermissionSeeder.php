<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'roles.view',
            'roles.manage',
            'permissions.view',
            'permissions.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        $roles = [
            'super_admin',
            'admin',
            'manager',
            'viewer',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'sanctum']);
        }

        Role::where('name', 'super_admin')->first()?->syncPermissions($permissions);
        Role::where('name', 'admin')->first()?->syncPermissions($permissions);
        Role::where('name', 'manager')->first()?->syncPermissions([
            'users.view',
            'users.update',
            'roles.view',
            'permissions.view',
        ]);
        Role::where('name', 'viewer')->first()?->syncPermissions([
            'users.view',
            'roles.view',
            'permissions.view',
        ]);

        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $adminPassword = env('ADMIN_PASSWORD', 'password');

        $admin = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => env('ADMIN_NAME', 'Admin'),
                'password' => Hash::make($adminPassword),
                'email_verified_at' => now(),
            ]
        );

        $admin->syncRoles(['super_admin']);
        $admin->syncPermissions($permissions);
    }
}

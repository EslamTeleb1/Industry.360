<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            CareerSeeder::class,
            ServicesSeeder::class,
            ContactIndustrySeeder::class,
            ContactServiceSeeder::class,
            ContactSolutionSeeder::class,
            PackageSeeder::class,
            HomeSettingSeeder::class,
        ]);

        // User::factory(10)->create();
    }
}

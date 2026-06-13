<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeamSetting;

class TeamSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TeamSetting::create([
            'title' => [
                'en' => 'Our Team',
                'ar' => 'فريقنا',
            ],
            'description' => [
                'en' => 'This is the team description.',
                'ar' => 'هذا وصف الفريق.',
            ],
        ]);


    }
}

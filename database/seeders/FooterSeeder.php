<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FooterItem;

class FooterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // clear existing (for seeding convenience)
        FooterItem::truncate();

        FooterItem::create([
            'type' => 'social',
            'platform' => 'twitter',
            'label' => 'Twitter',
            'url' => 'https://twitter.com/yourhandle',
            'order' => 1,
            'active' => true,
        ]);

        FooterItem::create([
            'type' => 'social',
            'platform' => 'facebook',
            'label' => 'Facebook',
            'url' => 'https://facebook.com/yourpage',
            'order' => 2,
            'active' => true,
        ]);

        FooterItem::create([
            'type' => 'social',
            'platform' => 'linkedin',
            'label' => 'LinkedIn',
            'url' => 'https://linkedin.com/company/yourcompany',
            'order' => 3,
            'active' => true,
        ]);

        FooterItem::create([
            'type' => 'image',
            'image_path' => '/images/footer/logo1.svg',
            'order' => 1,
            'active' => true,
        ]);

        FooterItem::create([
            'type' => 'image',
            'image_path' => '/images/footer/logo2.svg',
            'order' => 2,
            'active' => true,
        ]);
    }
}

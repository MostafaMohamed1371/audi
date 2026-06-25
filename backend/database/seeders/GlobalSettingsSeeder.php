<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Models\SocialLink;
use Illuminate\Database\Seeder;

class GlobalSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $generalSettings = [
            [
                'key' => 'site.name',
                'value_ar' => 'المعهد العربي لإنماء المدن',
                'value_en' => 'Arab Urban Development Institute',
            ],
            [
                'key' => 'site.copyright',
                'value_ar' => '© AUDI – جميع الحقوق محفوظة',
                'value_en' => '© AUDI – All rights reserved',
            ],
        ];

        foreach ($generalSettings as $row) {
            SiteSetting::query()->updateOrCreate(
                ['key' => $row['key']],
                [
                    'value_ar' => $row['value_ar'],
                    'value_en' => $row['value_en'],
                    'group' => 'general',
                ],
            );
        }

        $socialLinks = [
            ['platform' => 'facebook', 'url' => 'https://facebook.com', 'icon' => 'facebook', 'sort_order' => 0],
            ['platform' => 'linkedin', 'url' => 'https://linkedin.com', 'icon' => 'linkedin', 'sort_order' => 1],
            ['platform' => 'youtube', 'url' => 'https://youtube.com', 'icon' => 'youtube', 'sort_order' => 2],
            ['platform' => 'instagram', 'url' => 'https://instagram.com', 'icon' => 'instagram', 'sort_order' => 3],
            ['platform' => 'x', 'url' => 'https://x.com', 'icon' => 'x', 'sort_order' => 4],
        ];

        foreach ($socialLinks as $link) {
            SocialLink::query()->updateOrCreate(
                ['platform' => $link['platform']],
                array_merge($link, ['is_active' => true]),
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Enums\MemberCityStatKey;
use App\Enums\UserRole;
use App\Models\MemberCityStat;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            MemberCityStatsSeeder::class,
            ContactInfoSeeder::class,
            GlobalSettingsSeeder::class,
            MemberCitiesGeoJsonSeeder::class,
            MediaArticlesSeeder::class,
            ResourcesSeeder::class,
            AboutAndStrategySeeder::class,
            ProgramsSeeder::class,
            HomeSeeder::class,
            FooterFeaturesSeeder::class,
        ]);
    }
}

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@araburban.org'],
            [
                'name' => 'AUDI Admin',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'editor@araburban.org'],
            [
                'name' => 'AUDI Editor',
                'password' => Hash::make('password'),
                'role' => UserRole::Editor,
            ],
        );
    }
}

class MemberCityStatsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            [
                'key' => MemberCityStatKey::Countries->value,
                'value' => 12,
                'label_ar' => 'الدول',
                'label_en' => 'Countries',
                'unit_ar' => 'دولة',
                'unit_en' => 'countries',
                'auto_calculate' => false,
            ],
            [
                'key' => MemberCityStatKey::Cities->value,
                'value' => null,
                'label_ar' => 'المدن',
                'label_en' => 'Cities',
                'unit_ar' => 'مدينة',
                'unit_en' => 'cities',
                'auto_calculate' => true,
            ],
            [
                'key' => MemberCityStatKey::Members->value,
                'value' => 1240,
                'label_ar' => 'الاعضاء',
                'label_en' => 'Members',
                'unit_ar' => 'عضو',
                'unit_en' => 'members',
                'auto_calculate' => false,
            ],
        ];

        foreach ($defaults as $row) {
            MemberCityStat::query()->updateOrCreate(['key' => $row['key']], $row);
        }
    }
}

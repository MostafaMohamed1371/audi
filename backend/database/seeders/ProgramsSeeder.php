<?php

namespace Database\Seeders;

use App\Models\AboutContent;
use App\Models\DirectoryCity;
use App\Models\DirectoryOrganization;
use App\Models\DirectoryProject;
use App\Models\DirectoryPublication;
use App\Models\Expert;
use App\Models\Program;
use App\Models\ProgramSection;
use App\Models\TrainingCourse;
use App\Support\ImageUrl;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProgramsSeeder extends Seeder
{
    private const PROGRAMS = [
        'training' => 'training',
        'urban-policies' => 'urbanPolicies',
        'partnerships' => 'partnerships',
    ];

    /** @var array<int, string> Display order on the home page. */
    private const HOME_PROGRAM_SLUGS = ['urban-policies', 'training', 'partnerships'];

    /** @var array<string, array<string, string>> */
    private const SECTION_TABS = [
        'training' => ['trainingPrograms', 'consulting', 'executive', 'experts'],
        'urban-policies' => ['developmentPortal', 'developmentIndex', 'innovationLab', 'practiceReports'],
        'partnerships' => ['euroArabDialogue', 'secretarySpeaks', 'urbanAwards', 'partnersGuide'],
    ];

    public function run(): void
    {
        $ar = $this->loadJson('ar');
        $en = $this->loadJson('en');

        foreach (self::PROGRAMS as $slug => $jsonKey) {
            $this->seedProgram($slug, $jsonKey, $ar, $en);
        }

        $this->seedTrainingCourses($ar, $en);
        $this->seedExperts($ar, $en);
        $this->seedDirectory($ar, $en);

        $this->command?->info('Programs seeded.');
    }

    /**
     * @param  array<string, mixed>  $ar
     * @param  array<string, mixed>  $en
     */
    private function seedProgram(string $slug, string $jsonKey, array $ar, array $en): void
    {
        $arProgram = $ar[$jsonKey] ?? [];
        $enProgram = $en[$jsonKey] ?? [];

        Program::query()->updateOrCreate(
            ['slug' => $slug],
            [
                'title_ar' => $ar['pages'][$this->pageKey($slug)] ?? $slug,
                'title_en' => $en['pages'][$this->pageKey($slug)] ?? $slug,
                'hero_intro_ar' => $arProgram['heroIntro'] ?? null,
                'hero_intro_en' => $enProgram['heroIntro'] ?? null,
                'card_description_ar' => $this->homeProgramCard('ar', $slug)['description'] ?? null,
                'card_description_en' => $this->homeProgramCard('en', $slug)['description'] ?? null,
                'sort_order' => ($index = array_search($slug, self::HOME_PROGRAM_SLUGS, true)) !== false ? $index : 0,
            ],
        );

        AboutContent::query()->updateOrCreate(
            ['section_key' => 'program_'.$slug],
            [
                'body_ar' => [
                    'back' => $arProgram['back'] ?? null,
                    'sectionsLabel' => $arProgram['sectionsLabel'] ?? null,
                ],
                'body_en' => [
                    'back' => $enProgram['back'] ?? null,
                    'sectionsLabel' => $enProgram['sectionsLabel'] ?? null,
                ],
            ],
        );

        $program = Program::query()->where('slug', $slug)->firstOrFail();
        $tabs = self::SECTION_TABS[$slug] ?? [];

        foreach ($tabs as $index => $tabKey) {
            $arSection = $arProgram[$tabKey] ?? [];
            $enSection = $enProgram[$tabKey] ?? [];

            if ($tabKey === 'experts') {
                $arBody = ['title' => $arSection['title'] ?? ''];
                $enBody = ['title' => $enSection['title'] ?? ''];
            } else {
                $arBody = $this->sectionBody($arSection);
                $enBody = $this->sectionBody($enSection);
            }

            if ($tabKey === 'trainingPrograms') {
                unset($arBody['courses'], $enBody['courses']);
            }

            if ($tabKey === 'developmentPortal' && isset($arBody['directory']['rows'])) {
                unset($arBody['directory']['rows'], $enBody['directory']['rows']);
            }

            ProgramSection::query()->updateOrCreate(
                [
                    'program_id' => $program->id,
                    'tab_key' => $tabKey,
                ],
                [
                    'title_ar' => $arSection['title'] ?? ($arProgram['tabs'][$tabKey] ?? $tabKey),
                    'title_en' => $enSection['title'] ?? ($enProgram['tabs'][$tabKey] ?? $tabKey),
                    'intro_ar' => $arSection['intro'] ?? null,
                    'intro_en' => $enSection['intro'] ?? null,
                    'body_ar' => $arBody,
                    'body_en' => $enBody,
                    'image_url' => $arSection['image'] ?? null,
                    'sort_order' => $index,
                ],
            );
        }
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    private function sectionBody(array $section): array
    {
        $body = $section;
        unset($body['title'], $body['intro'], $body['image']);

        return $body;
    }

    /**
     * @param  array<string, mixed>  $ar
     * @param  array<string, mixed>  $en
     */
    private function seedTrainingCourses(array $ar, array $en): void
    {
        $arCourses = $ar['training']['trainingPrograms']['courses'] ?? [];
        $enCourses = $en['training']['trainingPrograms']['courses'] ?? [];

        foreach ($arCourses as $index => $course) {
            $enCourse = $enCourses[$index] ?? $course;

            TrainingCourse::query()->updateOrCreate(
                ['sort_order' => $index],
                [
                    'title_ar' => $course['title'] ?? '',
                    'title_en' => $enCourse['title'] ?? ($course['title'] ?? ''),
                    'count_ar' => $course['count'] ?? '',
                    'count_en' => $enCourse['count'] ?? ($course['count'] ?? ''),
                ],
            );
        }
    }

    /**
     * @param  array<string, mixed>  $ar
     * @param  array<string, mixed>  $en
     */
    private function seedExperts(array $ar, array $en): void
    {
        $arExperts = $ar['training']['experts']['experts'] ?? [];
        $enExperts = collect($en['training']['experts']['experts'] ?? [])->keyBy('name');

        foreach ($arExperts as $index => $expert) {
            $enExpert = $enExperts->get($expert['name'] ?? '', $expert);

            Expert::query()->updateOrCreate(
                ['sort_order' => $index],
                [
                    'name_ar' => $expert['name'] ?? '',
                    'name_en' => $enExpert['name'] ?? ($expert['name'] ?? ''),
                    'specialty_ar' => $expert['specialty'] ?? '',
                    'specialty_en' => $enExpert['specialty'] ?? ($expert['specialty'] ?? ''),
                    'image_url' => ImageUrl::publicAsset($expert['image'] ?? null, 'emp'),
                ],
            );
        }
    }

    /**
     * @param  array<string, mixed>  $ar
     * @param  array<string, mixed>  $en
     */
    private function seedDirectory(array $ar, array $en): void
    {
        $rows = $ar['urbanPolicies']['developmentPortal']['directory']['rows'] ?? [];

        foreach ($rows['cities'] ?? [] as $index => $row) {
            DirectoryCity::query()->updateOrCreate(
                ['number' => $row['number'] ?? (string) ($index + 1)],
                [
                    'name_ar' => $row['name'] ?? '',
                    'name_en' => $row['name'] ?? '',
                    'description_ar' => $row['description'] ?? null,
                    'description_en' => $row['description'] ?? null,
                    'country_code' => null,
                    'city_size' => $this->inferCitySize($row['description'] ?? ''),
                ],
            );
        }

        foreach ($rows['projects'] ?? [] as $index => $row) {
            DirectoryProject::query()->updateOrCreate(
                ['number' => $row['number'] ?? (string) ($index + 1)],
                [
                    'city_ar' => $row['city'] ?? '',
                    'city_en' => $row['city'] ?? '',
                    'country_ar' => $row['country'] ?? '',
                    'country_en' => $row['country'] ?? '',
                    'start_date' => $row['startDate'] ?? null,
                    'end_date' => $row['endDate'] ?? null,
                ],
            );
        }

        foreach ($rows['organizations'] ?? [] as $index => $row) {
            DirectoryOrganization::query()->updateOrCreate(
                ['number' => $row['number'] ?? (string) ($index + 1)],
                [
                    'name_ar' => $row['name'] ?? '',
                    'name_en' => $row['name'] ?? '',
                    'description_ar' => $row['description'] ?? null,
                    'description_en' => $row['description'] ?? null,
                ],
            );
        }

        foreach ($rows['publications'] ?? [] as $index => $row) {
            DirectoryPublication::query()->updateOrCreate(
                ['number' => $row['number'] ?? (string) ($index + 1)],
                [
                    'name_ar' => $row['name'] ?? '',
                    'name_en' => $row['name'] ?? '',
                    'description_ar' => $row['description'] ?? null,
                    'description_en' => $row['description'] ?? null,
                ],
            );
        }
    }

    private function pageKey(string $slug): string
    {
        return match ($slug) {
            'urban-policies' => 'urbanPolicies',
            'training' => 'training',
            'partnerships' => 'partnerships',
            default => $slug,
        };
    }

    private function inferCitySize(string $description): ?string
    {
        if (str_contains($description, 'كبيرة') || str_contains($description, 'large')) {
            return 'large';
        }

        if (str_contains($description, 'متوسط') || str_contains($description, 'medium')) {
            return 'medium';
        }

        if (str_contains($description, 'صغيرة') || str_contains($description, 'small')) {
            return 'small';
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function homeProgramCard(string $locale, string $slug): array
    {
        static $cache = [];

        if (! isset($cache[$locale])) {
            $path = dirname(base_path())."/messages/{$locale}/home.json";
            $items = [];

            if (File::exists($path)) {
                $home = json_decode(File::get($path), true) ?? [];
                $items = $home['programs']['items'] ?? [];
            }

            $cache[$locale] = [];

            foreach (self::HOME_PROGRAM_SLUGS as $index => $programSlug) {
                if (isset($items[$index]) && is_array($items[$index])) {
                    $cache[$locale][$programSlug] = $items[$index];
                }
            }
        }

        return $cache[$locale][$slug] ?? [];
    }

    /**
     * @return array<string, mixed>
     */
    private function loadJson(string $locale): array
    {
        $path = base_path("../messages/{$locale}/programs.json");

        if (! File::exists($path)) {
            return [];
        }

        return json_decode(File::get($path), true) ?? [];
    }
}

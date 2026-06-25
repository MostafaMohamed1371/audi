<?php

namespace Database\Seeders;

use App\Models\AboutContent;
use App\Models\HomeHeroSlide;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class HomeSeeder extends Seeder
{
    private const PROGRAM_SLUGS = ['urban-policies', 'training', 'partnerships'];

    public function run(): void
    {
        $ar = $this->loadJson('ar');
        $en = $this->loadJson('en');

        $this->seedHeroSlides($ar, $en);
        $this->seedAboutContent($ar, $en);

        $this->command?->info('Home content seeded.');
    }

    /**
     * @param  array<string, mixed>  $ar
     * @param  array<string, mixed>  $en
     */
    private function seedHeroSlides(array $ar, array $en): void
    {
        $arSlides = $ar['slider']['slides'] ?? [];
        $enSlides = collect($en['slider']['slides'] ?? [])->keyBy(fn ($_, $index) => $index);

        foreach ($arSlides as $index => $slide) {
            $enSlide = $enSlides->get($index, $slide);

            HomeHeroSlide::query()->updateOrCreate(
                ['sort_order' => $index],
                [
                    'title_ar' => $slide['title'] ?? '',
                    'title_en' => $enSlide['title'] ?? ($slide['title'] ?? ''),
                    'image_url' => '/slider/'.($index + 1).'.png',
                    'is_active' => true,
                ],
            );
        }
    }

    /**
     * @param  array<string, mixed>  $ar
     * @param  array<string, mixed>  $en
     */
    private function seedAboutContent(array $ar, array $en): void
    {
        AboutContent::query()->updateOrCreate(['section_key' => 'home_stats'], [
            'title_ar' => $ar['stats']['title'] ?? '',
            'title_en' => $en['stats']['title'] ?? '',
            'body_ar' => ['subtitle' => $ar['stats']['subtitle'] ?? ''],
            'body_en' => ['subtitle' => $en['stats']['subtitle'] ?? ''],
        ]);

        AboutContent::query()->updateOrCreate(['section_key' => 'home_about_intro'], [
            'title_ar' => $ar['aboutIntro']['title'] ?? '',
            'title_en' => $en['aboutIntro']['title'] ?? '',
            'body_ar' => [
                'description' => $ar['aboutIntro']['description'] ?? '',
                'cta' => $ar['aboutIntro']['cta'] ?? '',
                'mission' => $ar['aboutIntro']['mission'] ?? [],
                'vision' => $ar['aboutIntro']['vision'] ?? [],
            ],
            'body_en' => [
                'description' => $en['aboutIntro']['description'] ?? '',
                'cta' => $en['aboutIntro']['cta'] ?? '',
                'mission' => $en['aboutIntro']['mission'] ?? [],
                'vision' => $en['aboutIntro']['vision'] ?? [],
            ],
        ]);

        AboutContent::query()->updateOrCreate(['section_key' => 'home_member_cities'], [
            'title_ar' => $ar['memberCities']['title'] ?? '',
            'title_en' => $en['memberCities']['title'] ?? '',
        ]);

        AboutContent::query()->updateOrCreate(['section_key' => 'home_programs'], [
            'title_ar' => $ar['programs']['title'] ?? '',
            'title_en' => $en['programs']['title'] ?? '',
            'body_ar' => [
                'cta' => $ar['programs']['cta'] ?? '',
                'items' => $this->mapProgramItems($ar['programs']['items'] ?? []),
            ],
            'body_en' => [
                'cta' => $en['programs']['cta'] ?? '',
                'items' => $this->mapProgramItems($en['programs']['items'] ?? []),
            ],
        ]);

        AboutContent::query()->updateOrCreate(['section_key' => 'home_media_center'], [
            'title_ar' => $ar['mediaCenter']['title'] ?? '',
            'title_en' => $en['mediaCenter']['title'] ?? '',
            'body_ar' => [
                'subtitle' => $ar['mediaCenter']['subtitle'] ?? '',
                'readMore' => $ar['mediaCenter']['readMore'] ?? '',
                'viewAll' => $ar['mediaCenter']['viewAll'] ?? '',
            ],
            'body_en' => [
                'subtitle' => $en['mediaCenter']['subtitle'] ?? '',
                'readMore' => $en['mediaCenter']['readMore'] ?? '',
                'viewAll' => $en['mediaCenter']['viewAll'] ?? '',
            ],
        ]);

        AboutContent::query()->updateOrCreate(['section_key' => 'home_knowledge_center'], [
            'body_ar' => [
                'viewIssue' => $ar['knowledgeCenter']['viewIssue'] ?? '',
                'downloadPdf' => $ar['knowledgeCenter']['downloadPdf'] ?? '',
                'headerSlides' => $ar['knowledgeCenter']['headerSlides'] ?? [],
            ],
            'body_en' => [
                'viewIssue' => $en['knowledgeCenter']['viewIssue'] ?? '',
                'downloadPdf' => $en['knowledgeCenter']['downloadPdf'] ?? '',
                'headerSlides' => $en['knowledgeCenter']['headerSlides'] ?? [],
            ],
        ]);

        AboutContent::query()->updateOrCreate(['section_key' => 'home_membership_contact'], [
            'body_ar' => [
                'membership' => array_merge($ar['membershipContact']['membership'] ?? [], [
                    'href' => '/contact#membership',
                ]),
                'contact' => [
                    'title' => $ar['membershipContact']['contact']['title'] ?? '',
                    'addressTitle' => $ar['membershipContact']['contact']['addressTitle'] ?? '',
                ],
            ],
            'body_en' => [
                'membership' => array_merge($en['membershipContact']['membership'] ?? [], [
                    'href' => '/contact#membership',
                ]),
                'contact' => [
                    'title' => $en['membershipContact']['contact']['title'] ?? '',
                    'addressTitle' => $en['membershipContact']['contact']['addressTitle'] ?? '',
                ],
            ],
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private function mapProgramItems(array $items): array
    {
        return collect($items)
            ->values()
            ->map(function (array $item, int $index) {
                $slug = self::PROGRAM_SLUGS[$index] ?? null;

                return [
                    'slug' => $slug,
                    'title' => $item['title'] ?? '',
                    'description' => $item['description'] ?? '',
                    'href' => $slug ? "/programs/{$slug}" : ($item['href'] ?? '#programs'),
                ];
            })
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function loadJson(string $locale): array
    {
        $path = dirname(base_path())."/messages/{$locale}/home.json";

        if (! File::exists($path)) {
            return [];
        }

        return json_decode(File::get($path), true) ?? [];
    }
}

<?php

namespace Database\Seeders;

use App\Enums\LeadershipType;
use App\Models\AboutContent;
use App\Models\AdvisoryBoardMember;
use App\Models\FocusArea;
use App\Models\HomeStat;
use App\Models\LeadershipMessage;
use App\Models\Partner;
use App\Models\PartnerCategory;
use App\Models\StrategyDiagramItem;
use App\Models\StrategyPage;
use App\Models\StrategyPillar;
use App\Models\TeamMember;
use App\Models\TeamSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class AboutAndStrategySeeder extends Seeder
{
    public function run(): void
    {
        $arAbout = $this->loadJson('about', 'ar');
        $enAbout = $this->loadJson('about', 'en');
        $arStrategy = $this->loadJson('strategy', 'ar');
        $enStrategy = $this->loadJson('strategy', 'en');
        $arHome = $this->loadJson('home', 'ar');
        $enHome = $this->loadJson('home', 'en');

        $this->seedAboutContent($arAbout, $enAbout);
        $this->seedHomeStats($arHome, $enHome);
        $this->seedLeadership($arAbout, $enAbout);
        $this->seedAdvisoryBoard($arAbout, $enAbout);
        $this->seedTeam($arAbout, $enAbout);
        $this->seedPartners($arAbout, $enAbout);
        $this->seedStrategy($arStrategy, $enStrategy);

        $this->command?->info('About and strategy content seeded.');
    }

    /**
     * @param  array<string, mixed>  $ar
     * @param  array<string, mixed>  $en
     */
    private function seedAboutContent(array $ar, array $en): void
    {
        AboutContent::query()->updateOrCreate(['section_key' => 'institute'], [
            'title_ar' => $ar['institute']['heading'] ?? '',
            'title_en' => $en['institute']['heading'] ?? '',
            'body_ar' => [
                'paragraphs' => [
                    $ar['institute']['paragraph1'] ?? '',
                    $ar['institute']['paragraph2'] ?? '',
                ],
                'headquartersTitle' => $ar['institute']['headquartersTitle'] ?? '',
            ],
            'body_en' => [
                'paragraphs' => [
                    $en['institute']['paragraph1'] ?? '',
                    $en['institute']['paragraph2'] ?? '',
                ],
                'headquartersTitle' => $en['institute']['headquartersTitle'] ?? '',
            ],
        ]);

        AboutContent::query()->updateOrCreate(['section_key' => 'about_stats'], [
            'title_ar' => $ar['stats']['title'] ?? '',
            'title_en' => $en['stats']['title'] ?? '',
        ]);

        AboutContent::query()->updateOrCreate(['section_key' => 'tasks'], [
            'title_ar' => $ar['tasks']['title'] ?? '',
            'title_en' => $en['tasks']['title'] ?? '',
            'body_ar' => ['items' => $ar['tasks']['items'] ?? []],
            'body_en' => ['items' => $en['tasks']['items'] ?? []],
        ]);

        AboutContent::query()->updateOrCreate(['section_key' => 'vision_mission'], [
            'body_ar' => [
                'visionTitle' => $ar['visionMission']['visionTitle'] ?? '',
                'visionText' => $ar['visionMission']['visionText'] ?? '',
                'missionTitle' => $ar['visionMission']['missionTitle'] ?? '',
                'missionText' => $ar['visionMission']['missionText'] ?? '',
                'readMore' => $ar['visionMission']['readMore'] ?? '',
                'visionImage' => '/vision-mission/1.png',
                'missionImage' => '/vision-mission/2.png',
            ],
            'body_en' => [
                'visionTitle' => $en['visionMission']['visionTitle'] ?? '',
                'visionText' => $en['visionMission']['visionText'] ?? '',
                'missionTitle' => $en['visionMission']['missionTitle'] ?? '',
                'missionText' => $en['visionMission']['missionText'] ?? '',
                'readMore' => $en['visionMission']['readMore'] ?? '',
                'visionImage' => '/vision-mission/1.png',
                'missionImage' => '/vision-mission/2.png',
            ],
        ]);

        AboutContent::query()->updateOrCreate(['section_key' => 'goals'], [
            'title_ar' => $ar['goals']['title'] ?? '',
            'title_en' => $en['goals']['title'] ?? '',
            'body_ar' => ['items' => $ar['goals']['items'] ?? []],
            'body_en' => ['items' => $en['goals']['items'] ?? []],
        ]);

        AboutContent::query()->updateOrCreate(['section_key' => 'values'], [
            'title_ar' => $ar['values']['title'] ?? '',
            'title_en' => $en['values']['title'] ?? '',
            'body_ar' => ['items' => $ar['values']['items'] ?? []],
            'body_en' => ['items' => $en['values']['items'] ?? []],
        ]);

        AboutContent::query()->updateOrCreate(['section_key' => 'structure'], [
            'image_url' => '/operational-structure.png',
            'body_ar' => ['imageAlt' => $ar['structure']['imageAlt'] ?? ''],
            'body_en' => ['imageAlt' => $en['structure']['imageAlt'] ?? ''],
        ]);

        AboutContent::query()->updateOrCreate(['section_key' => 'partners_hero'], [
            'body_ar' => ['description' => $ar['partners']['heroDescription'] ?? ''],
            'body_en' => ['description' => $en['partners']['heroDescription'] ?? ''],
        ]);

        AboutContent::query()->updateOrCreate(['section_key' => 'advisory_board'], [
            'body_ar' => ['readMore' => $ar['advisoryBoard']['readMore'] ?? ''],
            'body_en' => ['readMore' => $en['advisoryBoard']['readMore'] ?? ''],
        ]);

        AboutContent::query()->updateOrCreate(['section_key' => 'team'], [
            'body_ar' => ['readMore' => $ar['team']['readMore'] ?? ''],
            'body_en' => ['readMore' => $en['team']['readMore'] ?? ''],
        ]);
    }

    /**
     * @param  array<string, mixed>  $ar
     * @param  array<string, mixed>  $en
     */
    private function seedHomeStats(array $ar, array $en): void
    {
        $arItems = $ar['stats']['items'] ?? [];
        $enItems = collect($en['stats']['items'] ?? [])->keyBy('description');

        foreach ($arItems as $index => $item) {
            $enItem = $enItems->get($item['description'] ?? '', $item);

            HomeStat::query()->updateOrCreate(
                ['sort_order' => $index],
                [
                    'value' => $item['value'] ?? '',
                    'label_ar' => $item['label'] ?? '',
                    'label_en' => $enItem['label'] ?? ($item['label'] ?? ''),
                    'description_ar' => $item['description'] ?? '',
                    'description_en' => $enItem['description'] ?? ($item['description'] ?? ''),
                ],
            );
        }
    }

    /**
     * @param  array<string, mixed>  $ar
     * @param  array<string, mixed>  $en
     */
    private function seedLeadership(array $ar, array $en): void
    {
        LeadershipMessage::query()->updateOrCreate(
            ['type' => LeadershipType::President->value],
            [
                'name_ar' => $ar['presidentSpeech']['name'] ?? '',
                'name_en' => $en['presidentSpeech']['name'] ?? '',
                'position_ar' => $ar['presidentSpeech']['position'] ?? '',
                'position_en' => $en['presidentSpeech']['position'] ?? '',
                'honorific_ar' => $ar['presidentSpeech']['honorific'] ?? null,
                'honorific_en' => $en['presidentSpeech']['honorific'] ?? null,
                'quote_ar' => $ar['presidentSpeech']['quote'] ?? '',
                'quote_en' => $en['presidentSpeech']['quote'] ?? '',
                'paragraphs_ar' => $ar['presidentSpeech']['paragraphs'] ?? [],
                'paragraphs_en' => $en['presidentSpeech']['paragraphs'] ?? [],
                'image_url' => '/emp/1.png',
                'image_alt_ar' => $ar['presidentSpeech']['imageAlt'] ?? null,
                'image_alt_en' => $en['presidentSpeech']['imageAlt'] ?? null,
            ],
        );

        LeadershipMessage::query()->updateOrCreate(
            ['type' => LeadershipType::Director->value],
            [
                'name_ar' => $ar['directorMessage']['name'] ?? '',
                'name_en' => $en['directorMessage']['name'] ?? '',
                'position_ar' => $ar['directorMessage']['position'] ?? '',
                'position_en' => $en['directorMessage']['position'] ?? '',
                'quote_ar' => $ar['directorMessage']['quote'] ?? '',
                'quote_en' => $en['directorMessage']['quote'] ?? '',
                'paragraphs_ar' => $ar['directorMessage']['paragraphs'] ?? [],
                'paragraphs_en' => $en['directorMessage']['paragraphs'] ?? [],
                'image_url' => '/emp/2.png',
                'image_alt_ar' => $ar['directorMessage']['imageAlt'] ?? null,
                'image_alt_en' => $en['directorMessage']['imageAlt'] ?? null,
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $ar
     * @param  array<string, mixed>  $en
     */
    private function seedAdvisoryBoard(array $ar, array $en): void
    {
        $enMembers = collect($en['advisoryBoard']['members'] ?? [])->keyBy('id');

        foreach ($ar['advisoryBoard']['members'] ?? [] as $index => $member) {
            $enMember = $enMembers->get($member['id'] ?? '', $member);

            AdvisoryBoardMember::query()->updateOrCreate(
                ['sort_order' => $index],
                [
                    'name_ar' => $member['name'] ?? '',
                    'name_en' => $enMember['name'] ?? ($member['name'] ?? ''),
                    'role_ar' => $member['role'] ?? '',
                    'role_en' => $enMember['role'] ?? ($member['role'] ?? ''),
                    'bio_ar' => $member['bio'] ?? null,
                    'bio_en' => $enMember['bio'] ?? ($member['bio'] ?? null),
                    'image_url' => $member['image'] ?? null,
                    'is_featured' => (bool) ($member['featured'] ?? false),
                ],
            );
        }
    }

    /**
     * @param  array<string, mixed>  $ar
     * @param  array<string, mixed>  $en
     */
    private function seedTeam(array $ar, array $en): void
    {
        $enSections = collect($en['team']['sections'] ?? [])->keyBy('id');

        foreach ($ar['team']['sections'] ?? [] as $sectionIndex => $section) {
            $enSection = $enSections->get($section['id'] ?? '', $section);

            $teamSection = TeamSection::query()->updateOrCreate(
                ['slug' => $section['id']],
                [
                    'title_ar' => $section['title'] ?? '',
                    'title_en' => $enSection['title'] ?? ($section['title'] ?? ''),
                    'sort_order' => $sectionIndex,
                ],
            );

            $enMembers = collect($enSection['members'] ?? [])->keyBy('id');

            foreach ($section['members'] ?? [] as $memberIndex => $member) {
                $enMember = $enMembers->get($member['id'] ?? '', $member);

                TeamMember::query()->updateOrCreate(
                    [
                        'team_section_id' => $teamSection->id,
                        'sort_order' => $memberIndex,
                    ],
                    [
                        'name_ar' => $member['name'] ?? '',
                        'name_en' => $enMember['name'] ?? ($member['name'] ?? ''),
                        'role_ar' => $member['role'] ?? '',
                        'role_en' => $enMember['role'] ?? ($member['role'] ?? ''),
                        'bio_ar' => $member['bio'] ?? null,
                        'bio_en' => $enMember['bio'] ?? ($member['bio'] ?? null),
                        'image_url' => $member['image'] ?? null,
                    ],
                );
            }
        }
    }

    /**
     * @param  array<string, mixed>  $ar
     * @param  array<string, mixed>  $en
     */
    private function seedPartners(array $ar, array $en): void
    {
        Partner::query()->where('is_featured', true)->delete();

        foreach ($ar['partners']['featured'] ?? [] as $index => $logo) {
            Partner::query()->create([
                'partner_category_id' => null,
                'name_ar' => $logo['name'] ?? '',
                'name_en' => $logo['name'] ?? '',
                'logo_url' => $logo['image'] ?? null,
                'is_featured' => true,
                'sort_order' => $index,
            ]);
        }

        $enCategories = collect($en['partners']['categories'] ?? [])->keyBy('id');

        foreach ($ar['partners']['categories'] ?? [] as $categoryIndex => $category) {
            $enCategory = $enCategories->get($category['id'] ?? '', $category);

            $partnerCategory = PartnerCategory::query()->updateOrCreate(
                ['slug' => $category['id']],
                [
                    'title_ar' => $category['title'] ?? '',
                    'title_en' => $enCategory['title'] ?? ($category['title'] ?? ''),
                    'sort_order' => $categoryIndex,
                ],
            );

            Partner::query()->where('partner_category_id', $partnerCategory->id)->delete();

            foreach ($category['logos'] ?? [] as $logoIndex => $logo) {
                Partner::query()->create([
                    'partner_category_id' => $partnerCategory->id,
                    'name_ar' => $logo['name'] ?? '',
                    'name_en' => $logo['name'] ?? '',
                    'logo_url' => $logo['image'] ?? null,
                    'is_featured' => false,
                    'sort_order' => $logoIndex,
                ]);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $ar
     * @param  array<string, mixed>  $en
     */
    private function seedStrategy(array $ar, array $en): void
    {
        AboutContent::query()->updateOrCreate(['section_key' => 'focus_areas_pages'], [
            'body_ar' => $ar['focusAreas']['pages'] ?? [],
            'body_en' => $en['focusAreas']['pages'] ?? [],
        ]);

        StrategyPage::query()->updateOrCreate(
            ['slug' => 'strategy-2025'],
            [
                'booklet_title_ar' => $ar['strategy2025']['booklet'] ?? '',
                'booklet_title_en' => $en['strategy2025']['booklet'] ?? '',
                'booklet_pdf_url' => $ar['strategy2025']['bookletPdf'] ?? null,
                'intro_title_ar' => $ar['strategy2025']['introTitle'] ?? '',
                'intro_title_en' => $en['strategy2025']['introTitle'] ?? '',
                'intro_subtitle_ar' => $ar['strategy2025']['introSubtitle'] ?? '',
                'intro_subtitle_en' => $en['strategy2025']['introSubtitle'] ?? '',
            ],
        );

        foreach ($ar['strategy2025']['pillars'] ?? [] as $index => $pillar) {
            $enPillar = $en['strategy2025']['pillars'][$index] ?? $pillar;

            StrategyPillar::query()->updateOrCreate(
                ['sort_order' => $index],
                [
                    'number' => $pillar['number'] ?? '',
                    'text_ar' => $pillar['text'] ?? '',
                    'text_en' => $enPillar['text'] ?? ($pillar['text'] ?? ''),
                ],
            );
        }

        $enDiagramItems = collect($en['diagram']['items'] ?? [])->keyBy('id');

        foreach ($ar['diagram']['items'] ?? [] as $index => $item) {
            $enItem = $enDiagramItems->get($item['id'] ?? '', $item);

            StrategyDiagramItem::query()->updateOrCreate(
                ['item_key' => $item['id']],
                [
                    'title_ar' => $item['title'] ?? '',
                    'title_en' => $enItem['title'] ?? ($item['title'] ?? ''),
                    'content_ar' => $item['content'] ?? null,
                    'content_en' => $enItem['content'] ?? ($item['content'] ?? null),
                    'columns_ar' => $item['columns'] ?? null,
                    'columns_en' => $enItem['columns'] ?? ($item['columns'] ?? null),
                    'sort_order' => $index,
                ],
            );
        }

        $enFocusAreas = collect($en['focusAreas']['items'] ?? [])->keyBy('slug');

        foreach ($ar['focusAreas']['items'] ?? [] as $index => $area) {
            $enArea = $enFocusAreas->get($area['slug'] ?? '', $area);

            FocusArea::query()->updateOrCreate(
                ['slug' => $area['slug']],
                [
                    'number' => $area['number'] ?? '',
                    'title_ar' => $area['title'] ?? '',
                    'title_en' => $enArea['title'] ?? ($area['title'] ?? ''),
                    'highlight_ar' => $area['highlight'] ?? '',
                    'highlight_en' => $enArea['highlight'] ?? ($area['highlight'] ?? ''),
                    'tags_ar' => $area['tags'] ?? [],
                    'tags_en' => $enArea['tags'] ?? ($area['tags'] ?? []),
                    'description_ar' => $area['description'] ?? '',
                    'description_en' => $enArea['description'] ?? ($area['description'] ?? ''),
                    'list_image_url' => $area['listImage'] ?? null,
                    'detail_image_url' => $area['detailImage'] ?? null,
                    'is_published' => true,
                    'sort_order' => $index,
                ],
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function loadJson(string $file, string $locale): array
    {
        $path = base_path("../messages/{$locale}/{$file}.json");

        if (! File::exists($path)) {
            return [];
        }

        return json_decode(File::get($path), true) ?? [];
    }
}

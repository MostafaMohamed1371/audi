<?php

declare(strict_types=1);

namespace App\Services\About;

use App\Enums\LeadershipType;
use App\Models\AboutContent;
use App\Models\AdvisoryBoardMember;
use App\Models\HomeStat;
use App\Models\LeadershipMessage;
use App\Models\Partner;
use App\Models\PartnerCategory;
use App\Models\TeamSection;

class AboutService
{
    public function getInstitute(?string $locale = null): array
    {
        $isAr = $this->isAr($locale);
        $institute = $this->content('institute');
        $tasks = $this->content('tasks');
        $statsTitle = $this->content('about_stats');

        $body = $isAr ? ($institute?->body_ar ?? []) : ($institute?->body_en ?? []);
        $tasksBody = $isAr ? ($tasks?->body_ar ?? []) : ($tasks?->body_en ?? []);

        return [
            'heading' => $isAr ? $institute?->title_ar : $institute?->title_en,
            'paragraphs' => $body['paragraphs'] ?? [],
            'headquartersTitle' => $body['headquartersTitle'] ?? null,
            'statsTitle' => $isAr ? $statsTitle?->title_ar : $statsTitle?->title_en,
            'stats' => $this->mapHomeStats($isAr),
            'tasks' => [
                'title' => $isAr ? $tasks?->title_ar : $tasks?->title_en,
                'items' => $tasksBody['items'] ?? [],
            ],
        ];
    }

    public function getVisionMission(?string $locale = null): array
    {
        $isAr = $this->isAr($locale);
        $visionMission = $this->content('vision_mission');
        $goals = $this->content('goals');
        $values = $this->content('values');

        $body = $isAr ? ($visionMission?->body_ar ?? []) : ($visionMission?->body_en ?? []);
        $goalsBody = $isAr ? ($goals?->body_ar ?? []) : ($goals?->body_en ?? []);
        $valuesBody = $isAr ? ($values?->body_ar ?? []) : ($values?->body_en ?? []);

        return [
            'vision' => [
                'title' => $body['visionTitle'] ?? null,
                'text' => $body['visionText'] ?? null,
                'readMore' => $body['readMore'] ?? null,
                'image' => $body['visionImage'] ?? '/vision-mission/1.png',
            ],
            'mission' => [
                'title' => $body['missionTitle'] ?? null,
                'text' => $body['missionText'] ?? null,
                'readMore' => $body['readMore'] ?? null,
                'image' => $body['missionImage'] ?? '/vision-mission/2.png',
            ],
            'goals' => [
                'title' => $isAr ? $goals?->title_ar : $goals?->title_en,
                'items' => $goalsBody['items'] ?? [],
            ],
            'values' => [
                'title' => $isAr ? $values?->title_ar : $values?->title_en,
                'items' => $valuesBody['items'] ?? [],
            ],
        ];
    }

    public function getLeadership(string $type, ?string $locale = null): ?array
    {
        if (! in_array($type, [LeadershipType::President->value, LeadershipType::Director->value], true)) {
            return null;
        }

        $message = LeadershipMessage::query()->where('type', $type)->first();

        if (! $message) {
            return null;
        }

        $isAr = $this->isAr($locale);

        return [
            'honorific' => $isAr ? $message->honorific_ar : $message->honorific_en,
            'name' => $isAr ? $message->name_ar : $message->name_en,
            'position' => $isAr ? $message->position_ar : $message->position_en,
            'quote' => $isAr ? $message->quote_ar : $message->quote_en,
            'paragraphs' => $isAr ? $message->paragraphs_ar : $message->paragraphs_en,
            'image' => $message->image_url ?? ($type === LeadershipType::President->value ? '/emp/1.png' : '/emp/2.png'),
            'imageAlt' => $isAr ? $message->image_alt_ar : $message->image_alt_en,
        ];
    }

    public function getAdvisoryBoard(?string $locale = null): array
    {
        $isAr = $this->isAr($locale);
        $meta = $this->content('advisory_board');

        $members = AdvisoryBoardMember::query()
            ->ordered()
            ->get()
            ->map(fn (AdvisoryBoardMember $member) => [
                'id' => (string) $member->id,
                'featured' => $member->is_featured,
                'role' => $isAr ? $member->role_ar : $member->role_en,
                'name' => $isAr ? $member->name_ar : $member->name_en,
                'image' => basename((string) ($member->image_url ?? '')),
                'bio' => $isAr ? $member->bio_ar : $member->bio_en,
            ])
            ->values()
            ->all();

        $body = $isAr ? ($meta?->body_ar ?? []) : ($meta?->body_en ?? []);

        return [
            'readMore' => $body['readMore'] ?? null,
            'members' => $members,
        ];
    }

    public function getTeam(?string $locale = null): array
    {
        $isAr = $this->isAr($locale);
        $meta = $this->content('team');

        $sections = TeamSection::query()
            ->with('members')
            ->ordered()
            ->get()
            ->map(function (TeamSection $section) use ($isAr) {
                return [
                    'id' => $section->slug,
                    'title' => $isAr ? $section->title_ar : $section->title_en,
                    'members' => $section->members->map(fn ($member) => [
                        'id' => (string) $member->id,
                        'role' => $isAr ? $member->role_ar : $member->role_en,
                        'name' => $isAr ? $member->name_ar : $member->name_en,
                        'image' => basename((string) ($member->image_url ?? '')),
                        'bio' => $isAr ? $member->bio_ar : $member->bio_en,
                    ])->values()->all(),
                ];
            })
            ->values()
            ->all();

        $body = $isAr ? ($meta?->body_ar ?? []) : ($meta?->body_en ?? []);

        return [
            'readMore' => $body['readMore'] ?? null,
            'sections' => $sections,
        ];
    }

    public function getStructure(?string $locale = null): array
    {
        $isAr = $this->isAr($locale);
        $structure = $this->content('structure');
        $body = $isAr ? ($structure?->body_ar ?? []) : ($structure?->body_en ?? []);

        return [
            'imageUrl' => $structure?->image_url ?? '/operational-structure.png',
            'imageAlt' => $body['imageAlt'] ?? null,
        ];
    }

    public function getPartners(?string $locale = null): array
    {
        $isAr = $this->isAr($locale);
        $hero = $this->content('partners_hero');
        $body = $isAr ? ($hero?->body_ar ?? []) : ($hero?->body_en ?? []);

        $featured = Partner::query()
            ->where('is_featured', true)
            ->ordered()
            ->get()
            ->map(fn (Partner $partner) => [
                'image' => basename((string) ($partner->logo_url ?? '')),
                'name' => $isAr ? $partner->name_ar : $partner->name_en,
            ])
            ->values()
            ->all();

        $categories = PartnerCategory::query()
            ->with(['partners' => fn ($query) => $query->ordered()])
            ->ordered()
            ->get()
            ->map(fn (PartnerCategory $category) => [
                'id' => $category->slug,
                'title' => $isAr ? $category->title_ar : $category->title_en,
                'logos' => $category->partners->map(fn (Partner $partner) => [
                    'image' => basename((string) ($partner->logo_url ?? '')),
                    'name' => $isAr ? $partner->name_ar : $partner->name_en,
                ])->values()->all(),
            ])
            ->values()
            ->all();

        return [
            'heroDescription' => $body['description'] ?? null,
            'featured' => $featured,
            'categories' => $categories,
        ];
    }

    private function content(string $key): ?AboutContent
    {
        return AboutContent::query()->where('section_key', $key)->first();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function mapHomeStats(bool $isAr): array
    {
        return HomeStat::query()
            ->ordered()
            ->get()
            ->map(fn (HomeStat $stat) => [
                'value' => $stat->value,
                'label' => $isAr ? $stat->label_ar : $stat->label_en,
                'description' => $isAr ? $stat->description_ar : $stat->description_en,
            ])
            ->values()
            ->all();
    }

    private function isAr(?string $locale): bool
    {
        return ($locale ?? app()->getLocale()) === 'ar';
    }
}

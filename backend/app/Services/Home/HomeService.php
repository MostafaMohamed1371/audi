<?php

declare(strict_types=1);

namespace App\Services\Home;

use App\Enums\MediaCategory;
use App\Http\Resources\Api\V1\MediaArticleListResource;
use App\Http\Resources\Api\V1\ResourceItemResource;
use App\Models\AboutContent;
use App\Models\HomeHeroSlide;
use App\Models\HomeStat;
use App\Models\KnowledgeCategory;
use App\Models\MediaArticle;
use App\Models\Program;
use App\Models\Resource;
use App\Support\ImageUrl;
use App\Services\ContactInfoService;
use App\Services\MemberCities\MemberCityStatService;
use Illuminate\Http\Request;

class HomeService
{
    public function __construct(
        private readonly MemberCityStatService $memberCityStats,
        private readonly ContactInfoService $contactInfo,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function getHome(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $isAr = $locale === 'ar';

        return [
            'slider' => $this->slider($isAr),
            'aboutIntro' => $this->aboutIntro($isAr),
            'stats' => $this->stats($isAr),
            'memberCities' => $this->memberCities($locale, $isAr),
            'programs' => $this->programs($isAr),
            'mediaCenter' => $this->mediaCenter($locale, $isAr),
            'knowledgeCenter' => $this->knowledgeCenter($locale, $isAr),
            'membershipContact' => $this->membershipContact($isAr),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function slider(bool $isAr): array
    {
        return HomeHeroSlide::query()
            ->where('is_active', true)
            ->ordered()
            ->get()
            ->map(fn (HomeHeroSlide $slide) => [
                'title' => $isAr ? $slide->title_ar : $slide->title_en,
                'imageUrl' => ImageUrl::public($slide->image_url),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function aboutIntro(bool $isAr): array
    {
        $content = $this->content('home_about_intro');
        $body = $isAr ? ($content?->body_ar ?? []) : ($content?->body_en ?? []);

        return [
            'title' => $isAr ? $content?->title_ar : $content?->title_en,
            'description' => $body['description'] ?? '',
            'cta' => $body['cta'] ?? '',
            'mission' => $body['mission'] ?? [],
            'vision' => $body['vision'] ?? [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function stats(bool $isAr): array
    {
        $content = $this->content('home_stats');

        return [
            'title' => $isAr ? $content?->title_ar : $content?->title_en,
            'subtitle' => $isAr ? ($content?->body_ar['subtitle'] ?? '') : ($content?->body_en['subtitle'] ?? ''),
            'items' => HomeStat::query()
                ->ordered()
                ->get()
                ->map(fn (HomeStat $stat) => [
                    'value' => $stat->value,
                    'label' => $isAr ? $stat->label_ar : $stat->label_en,
                    'description' => $isAr ? $stat->description_ar : $stat->description_en,
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function memberCities(string $locale, bool $isAr): array
    {
        $content = $this->content('home_member_cities');

        return [
            'title' => $isAr ? $content?->title_ar : $content?->title_en,
            'stats' => $this->memberCityStats->getPublicStats($locale),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function programs(bool $isAr): array
    {
        $content = $this->content('home_programs');
        $body = $isAr ? ($content?->body_ar ?? []) : ($content?->body_en ?? []);

        $items = Program::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Program $program) => [
                'slug' => $program->slug,
                'title' => $isAr ? $program->title_ar : $program->title_en,
                'description' => $isAr
                    ? ($program->card_description_ar ?? $program->hero_intro_ar ?? '')
                    : ($program->card_description_en ?? $program->hero_intro_en ?? ''),
                'href' => '/programs/'.$program->slug,
            ])
            ->values()
            ->all();

        return [
            'title' => $isAr ? $content?->title_ar : $content?->title_en,
            'cta' => $body['cta'] ?? '',
            'items' => $items,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function mediaCenter(string $locale, bool $isAr): array
    {
        $content = $this->content('home_media_center');
        $body = $isAr ? ($content?->body_ar ?? []) : ($content?->body_en ?? []);

        $articles = MediaArticle::query()
            ->where('category', MediaCategory::News->value)
            ->where('is_published', true)
            ->orderByDesc('published_date')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(8)
            ->get();

        $request = Request::create('/', 'GET');
        $request->attributes->set('locale', $locale);

        $mapped = $articles
            ->map(fn (MediaArticle $article) => (new MediaArticleListResource($article))->toArray($request))
            ->values()
            ->all();

        return [
            'title' => $isAr ? $content?->title_ar : $content?->title_en,
            'subtitle' => $body['subtitle'] ?? '',
            'readMore' => $body['readMore'] ?? '',
            'viewAll' => $body['viewAll'] ?? '',
            'featured' => array_slice($mapped, 0, 4),
            'items' => array_slice($mapped, 4, 4),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function knowledgeCenter(string $locale, bool $isAr): array
    {
        $content = $this->content('home_knowledge_center');
        $body = $isAr ? ($content?->body_ar ?? []) : ($content?->body_en ?? []);

        $request = Request::create('/', 'GET');
        $request->attributes->set('locale', $locale);

        $categories = KnowledgeCategory::query()
            ->ordered()
            ->with(['resources' => function ($query) {
                $query
                    ->where('is_published', true)
                    ->orderByDesc('published_date')
                    ->orderBy('sort_order')
                    ->orderBy('id');
            }])
            ->get()
            ->map(function (KnowledgeCategory $category) use ($request, $isAr) {
                $items = $category->resources
                    ->map(function (Resource $resource) use ($request) {
                        $row = (new ResourceItemResource($resource))->toArray($request);

                        return [
                            'slug' => $row['slug'],
                            'title' => $row['title'],
                            'date' => $row['date'],
                            'href' => '/resources',
                            'pdfHref' => $row['downloadHref'] ?? '#',
                            'image' => $row['image'] ?? '',
                        ];
                    })
                    ->values()
                    ->all();

                return [
                    'id' => $category->id,
                    'slug' => $category->slug,
                    'title' => $isAr ? $category->title_ar : $category->title_en,
                    'description' => $isAr ? ($category->description_ar ?? '') : ($category->description_en ?? ''),
                    'items' => $items,
                ];
            })
            ->values()
            ->all();

        if ($categories !== []) {
            $headerSlides = array_map(static fn (array $category) => [
                'id' => $category['id'],
                'slug' => $category['slug'],
                'title' => $category['title'],
                'description' => $category['description'],
            ], $categories);

            $firstItems = $categories[0]['items'] ?? [];

            return [
                'viewIssue' => $body['viewIssue'] ?? '',
                'downloadPdf' => $body['downloadPdf'] ?? '',
                'categories' => $categories,
                'headerSlides' => $headerSlides,
                'items' => $firstItems,
            ];
        }

        $legacySlides = $body['headerSlides'] ?? [];

        $resources = Resource::query()
            ->where('is_published', true)
            ->orderByDesc('published_date')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(3)
            ->get();

        $items = $resources
            ->map(function (Resource $resource) use ($request) {
                $row = (new ResourceItemResource($resource))->toArray($request);

                return [
                    'slug' => $row['slug'],
                    'title' => $row['title'],
                    'date' => $row['date'],
                    'href' => '/resources',
                    'pdfHref' => $row['downloadHref'] ?? '#',
                    'image' => $row['image'] ?? '',
                ];
            })
            ->values()
            ->all();

        return [
            'viewIssue' => $body['viewIssue'] ?? '',
            'downloadPdf' => $body['downloadPdf'] ?? '',
            'categories' => [],
            'headerSlides' => $legacySlides,
            'items' => $items,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function membershipContact(bool $isAr): array
    {
        $content = $this->content('home_membership_contact');
        $body = $isAr ? ($content?->body_ar ?? []) : ($content?->body_en ?? []);
        $contact = $this->contactInfo->getPublicPayload($isAr ? 'ar' : 'en');

        return [
            'membership' => $body['membership'] ?? [],
            'contact' => array_merge($contact, [
                'title' => $body['contact']['title'] ?? $contact['title'] ?? '',
                'addressTitle' => $body['contact']['addressTitle'] ?? $contact['addressLabel'] ?? '',
            ]),
        ];
    }

    private function content(string $key): ?AboutContent
    {
        return AboutContent::query()->where('section_key', $key)->first();
    }
}

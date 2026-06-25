<?php

namespace Database\Seeders;

use App\Enums\MediaCategory;
use App\Models\MediaArticle;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class MediaArticlesSeeder extends Seeder
{
    private const CATEGORIES = [
        'news' => MediaCategory::News,
        'newsletter' => MediaCategory::Newsletter,
        'cityMeetings' => MediaCategory::CityMeetings,
    ];

    public function run(): void
    {
        $ar = $this->loadMessages('ar');
        $en = $this->loadMessages('en');

        $imported = 0;

        foreach (self::CATEGORIES as $jsonKey => $category) {
            $arItems = collect($ar[$jsonKey]['items'] ?? [])->keyBy('key');
            $enItems = collect($en[$jsonKey]['items'] ?? [])->keyBy('key');

            foreach ($arItems as $key => $arItem) {
                $enItem = $enItems->get($key, $arItem);

                MediaArticle::query()->updateOrCreate(
                    ['key' => $key],
                    [
                        'category' => $category->value,
                        'slug_ar' => $arItem['slug'],
                        'slug_en' => $enItem['slug'] ?? $arItem['slug'],
                        'title_ar' => $arItem['title'],
                        'title_en' => $enItem['title'] ?? $arItem['title'],
                        'description_ar' => $arItem['description'] ?? null,
                        'description_en' => $enItem['description'] ?? null,
                        'body_ar' => $arItem['body'] ?? [],
                        'body_en' => $enItem['body'] ?? [],
                        'published_date' => $this->parseDate($arItem['date'] ?? null),
                        'image_url' => $arItem['image'] ?? null,
                        'pdf_url' => $this->normalizeUrl($arItem['pdfHref'] ?? null),
                        'authors_ar' => $arItem['authors'] ?? null,
                        'authors_en' => $enItem['authors'] ?? ($arItem['authors'] ?? null),
                        'event_time' => $arItem['time'] ?? null,
                        'is_published' => true,
                        'sort_order' => $imported,
                    ],
                );

                $imported++;
            }
        }

        $this->command?->info("Media articles seeded: {$imported}");
    }

    /**
     * @return array<string, mixed>
     */
    private function loadMessages(string $locale): array
    {
        $path = base_path("../messages/{$locale}/media.json");

        if (! File::exists($path)) {
            return [];
        }

        return json_decode(File::get($path), true) ?? [];
    }

    private function parseDate(?string $date): ?string
    {
        if (! $date) {
            return null;
        }

        try {
            if (str_contains($date, '/')) {
                return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            }

            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizeUrl(?string $url): ?string
    {
        if ($url === null || $url === '' || $url === '#') {
            return null;
        }

        return $url;
    }
}

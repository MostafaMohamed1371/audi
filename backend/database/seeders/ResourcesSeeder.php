<?php

namespace Database\Seeders;

use App\Models\KnowledgeCategory;
use App\Models\Resource;
use App\Support\ImageUrl;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ResourcesSeeder extends Seeder
{
    public function run(): void
    {
        $ar = $this->loadMessages('ar');
        $en = $this->loadMessages('en');

        $arItems = collect($ar['items'] ?? [])->keyBy('slug');
        $enItems = collect($en['items'] ?? [])->keyBy('slug');

        $imported = 0;
        $knowledgeCenterCategoryId = KnowledgeCategory::query()
            ->where('slug', 'knowledge-center')
            ->value('id');

        foreach ($arItems as $slug => $arItem) {
            $enItem = $enItems->get($slug, $arItem);
            $publishedDate = $this->parseDate($arItem['date'] ?? null);

            Resource::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'title_ar' => $arItem['title'],
                    'title_en' => $enItem['title'] ?? $arItem['title'],
                    'published_date' => $publishedDate,
                    'image_url' => ImageUrl::publicAsset($arItem['image'] ?? null, 'our-sources'),
                    'file_url' => $this->normalizeUrl($arItem['downloadHref'] ?? null),
                    'resource_type' => null,
                    'focus_area_id' => null,
                    'knowledge_category_id' => $knowledgeCenterCategoryId,
                    'year' => $publishedDate ? (int) Carbon::parse($publishedDate)->format('Y') : null,
                    'is_published' => true,
                    'sort_order' => $imported,
                ],
            );

            $imported++;
        }

        $this->command?->info("Resources seeded: {$imported}");
    }

    /**
     * @return array<string, mixed>
     */
    private function loadMessages(string $locale): array
    {
        $path = base_path("../messages/{$locale}/resources.json");

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

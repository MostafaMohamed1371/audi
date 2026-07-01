<?php

namespace Database\Seeders;

use App\Models\KnowledgeCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class KnowledgeCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $ar = $this->loadJson('ar');
        $en = $this->loadJson('en');

        $arSlides = $ar['knowledgeCenter']['headerSlides'] ?? [];
        $enSlides = collect($en['knowledgeCenter']['headerSlides'] ?? [])->keyBy(fn ($_, $index) => $index);

        $slugs = ['knowledge-center', 'mudununa', 'meetings-platform'];

        foreach ($arSlides as $index => $slide) {
            $enSlide = $enSlides->get($index, $slide);

            KnowledgeCategory::query()->updateOrCreate(
                ['slug' => $slugs[$index] ?? 'category-'.$index],
                [
                    'title_ar' => $slide['title'] ?? '',
                    'title_en' => $enSlide['title'] ?? ($slide['title'] ?? ''),
                    'description_ar' => $slide['description'] ?? null,
                    'description_en' => $enSlide['description'] ?? ($slide['description'] ?? null),
                    'sort_order' => $index,
                ],
            );
        }

        $this->command?->info('Knowledge categories seeded: '.count($arSlides));
    }

    /**
     * @return array<string, mixed>
     */
    private function loadJson(string $locale): array
    {
        $path = base_path("../messages/{$locale}/home.json");

        if (! File::exists($path)) {
            return [];
        }

        return json_decode(File::get($path), true) ?? [];
    }
}

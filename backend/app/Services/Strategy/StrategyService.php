<?php

declare(strict_types=1);

namespace App\Services\Strategy;

use App\Models\FocusArea;
use App\Models\StrategyDiagramItem;
use App\Models\StrategyPage;
use App\Models\StrategyPillar;
use App\Support\ImageUrl;

class StrategyService
{
    public function getStrategy2025(?string $locale = null): array
    {
        $isAr = $this->isAr($locale);
        $page = StrategyPage::query()->where('slug', 'strategy-2025')->first();

        $pillars = StrategyPillar::query()
            ->ordered()
            ->get()
            ->map(fn (StrategyPillar $pillar) => [
                'number' => $pillar->number,
                'text' => $isAr ? $pillar->text_ar : $pillar->text_en,
            ])
            ->values()
            ->all();

        $diagramItems = StrategyDiagramItem::query()
            ->ordered()
            ->get()
            ->map(function (StrategyDiagramItem $item) use ($isAr) {
                $payload = [
                    'id' => $item->item_key,
                    'title' => $isAr ? $item->title_ar : $item->title_en,
                ];

                $columns = $isAr ? $item->columns_ar : $item->columns_en;
                $content = $isAr ? $item->content_ar : $item->content_en;

                if ($columns) {
                    $payload['columns'] = $columns;
                } elseif ($content) {
                    $payload['content'] = $content;
                }

                return $payload;
            })
            ->values()
            ->all();

        return [
            'introTitle' => $isAr ? $page?->intro_title_ar : $page?->intro_title_en,
            'introSubtitle' => $isAr ? $page?->intro_subtitle_ar : $page?->intro_subtitle_en,
            'booklet' => [
                'title' => $isAr ? $page?->booklet_title_ar : $page?->booklet_title_en,
                'pdfUrl' => ImageUrl::public($page?->booklet_pdf_url),
                'href' => '#strategy-booklet',
            ],
            'pillars' => $pillars,
            'diagram' => [
                'items' => $diagramItems,
            ],
        ];
    }

    public function getFocusAreas(?string $locale = null): array
    {
        $isAr = $this->isAr($locale);
        $meta = \App\Models\AboutContent::query()->where('section_key', 'focus_areas_pages')->first();
        $body = $isAr ? ($meta?->body_ar ?? []) : ($meta?->body_en ?? []);

        return [
            'pages' => [
                'title' => $body['title'] ?? null,
                'back' => $body['back'] ?? null,
                'viewMore' => $body['viewMore'] ?? null,
                'previous' => $body['previous'] ?? null,
                'next' => $body['next'] ?? null,
            ],
            'items' => $this->mapFocusAreas($isAr),
        ];
    }

    public function getFocusArea(string $slug, ?string $locale = null): ?array
    {
        $area = FocusArea::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (! $area) {
            return null;
        }

        $isAr = $this->isAr($locale);
        $items = FocusArea::query()
            ->where('is_published', true)
            ->ordered()
            ->get(['slug', 'title_ar', 'title_en']);

        $currentIndex = $items->search(fn (FocusArea $item) => $item->slug === $slug);
        $previous = $currentIndex > 0 ? $items[$currentIndex - 1] : null;
        $next = $currentIndex !== false && $currentIndex < $items->count() - 1
            ? $items[$currentIndex + 1]
            : null;

        return [
            'area' => $this->mapFocusArea($area, $isAr),
            'navigation' => [
                'previous' => $previous ? [
                    'slug' => $previous->slug,
                    'title' => $isAr ? $previous->title_ar : $previous->title_en,
                ] : null,
                'next' => $next ? [
                    'slug' => $next->slug,
                    'title' => $isAr ? $next->title_ar : $next->title_en,
                ] : null,
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function mapFocusAreas(bool $isAr): array
    {
        return FocusArea::query()
            ->where('is_published', true)
            ->ordered()
            ->get()
            ->map(fn (FocusArea $area) => $this->mapFocusArea($area, $isAr))
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function mapFocusArea(FocusArea $area, bool $isAr): array
    {
        return [
            'slug' => $area->slug,
            'number' => $area->number,
            'title' => $isAr ? $area->title_ar : $area->title_en,
            'highlight' => $isAr ? $area->highlight_ar : $area->highlight_en,
            'tags' => $isAr ? $area->tags_ar : $area->tags_en,
            'description' => $isAr ? $area->description_ar : $area->description_en,
            'listImage' => ImageUrl::public($area->list_image_url),
            'detailImage' => ImageUrl::public($area->detail_image_url),
        ];
    }

    private function isAr(?string $locale): bool
    {
        return ($locale ?? app()->getLocale()) === 'ar';
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\StrategyPage;
use App\Support\ImageUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StrategyPageController extends Controller
{
    public function show(StrategyPage $strategyPage): JsonResponse
    {
        return response()->json(['data' => $this->transform($strategyPage)]);
    }

    public function showDefault(): JsonResponse
    {
        $page = StrategyPage::query()->where('slug', 'strategy-2025')->firstOrFail();

        return $this->show($page);
    }

    public function updateDefault(Request $request): JsonResponse
    {
        $page = StrategyPage::query()->where('slug', 'strategy-2025')->firstOrFail();

        return $this->update($request, $page);
    }

    public function update(Request $request, StrategyPage $strategyPage): JsonResponse
    {
        $validated = $request->validate([
            'bookletTitleAr' => ['sometimes', 'nullable', 'string', 'max:255'],
            'bookletTitleEn' => ['sometimes', 'nullable', 'string', 'max:255'],
            'bookletPdfUrl' => ['sometimes', 'nullable', 'string', 'max:500'],
            'introTitleAr' => ['sometimes', 'nullable', 'string'],
            'introTitleEn' => ['sometimes', 'nullable', 'string'],
            'introSubtitleAr' => ['sometimes', 'nullable', 'string'],
            'introSubtitleEn' => ['sometimes', 'nullable', 'string'],
        ]);

        $payload = [];

        if (array_key_exists('bookletTitleAr', $validated)) {
            $payload['booklet_title_ar'] = $validated['bookletTitleAr'];
        }
        if (array_key_exists('bookletTitleEn', $validated)) {
            $payload['booklet_title_en'] = $validated['bookletTitleEn'];
        }
        if (array_key_exists('bookletPdfUrl', $validated)) {
            $payload['booklet_pdf_url'] = $validated['bookletPdfUrl'];
        }
        if (array_key_exists('introTitleAr', $validated)) {
            $payload['intro_title_ar'] = $validated['introTitleAr'];
        }
        if (array_key_exists('introTitleEn', $validated)) {
            $payload['intro_title_en'] = $validated['introTitleEn'];
        }
        if (array_key_exists('introSubtitleAr', $validated)) {
            $payload['intro_subtitle_ar'] = $validated['introSubtitleAr'];
        }
        if (array_key_exists('introSubtitleEn', $validated)) {
            $payload['intro_subtitle_en'] = $validated['introSubtitleEn'];
        }

        $strategyPage->update($payload);

        return response()->json(['data' => $this->transform($strategyPage->fresh())]);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(StrategyPage $page): array
    {
        return [
            'id' => $page->id,
            'slug' => $page->slug,
            'bookletTitleAr' => $page->booklet_title_ar,
            'bookletTitleEn' => $page->booklet_title_en,
            'bookletPdfUrl' => ImageUrl::api($page->booklet_pdf_url),
            'introTitleAr' => $page->intro_title_ar,
            'introTitleEn' => $page->intro_title_en,
            'introSubtitleAr' => $page->intro_subtitle_ar,
            'introSubtitleEn' => $page->intro_subtitle_en,
            'createdAt' => $page->created_at?->toIso8601String(),
            'updatedAt' => $page->updated_at?->toIso8601String(),
        ];
    }
}

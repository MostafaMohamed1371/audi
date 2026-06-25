<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LegalPage;
use Illuminate\Http\JsonResponse;

class LegalController extends Controller
{
    public function show(string $slug): JsonResponse
    {
        $page = LegalPage::query()->where('slug', $slug)->first();

        if (! $page) {
            return response()->json(['message' => 'Page not found.'], 404);
        }

        $isAr = app()->getLocale() === 'ar';

        return response()->json([
            'data' => [
                'slug' => $page->slug,
                'title' => $isAr ? $page->title_ar : $page->title_en,
                'content' => $isAr ? $page->content_ar : $page->content_en,
                'effectiveDate' => $page->effective_date?->toDateString(),
                'updatedAt' => $page->updated_at?->toIso8601String(),
            ],
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LegalPageController extends Controller
{
    public function index(): JsonResponse
    {
        $items = LegalPage::query()
            ->orderBy('slug')
            ->get()
            ->map(fn (LegalPage $page) => $this->transform($page))
            ->values();

        return response()->json(['data' => $items]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:60', Rule::unique('legal_pages', 'slug')],
            'titleAr' => ['required', 'string', 'max:255'],
            'titleEn' => ['required', 'string', 'max:255'],
            'contentAr' => ['required', 'string'],
            'contentEn' => ['required', 'string'],
            'effectiveDate' => ['nullable', 'date'],
        ]);

        $page = LegalPage::query()->create([
            'slug' => $validated['slug'],
            'title_ar' => $validated['titleAr'],
            'title_en' => $validated['titleEn'],
            'content_ar' => $validated['contentAr'],
            'content_en' => $validated['contentEn'],
            'effective_date' => $validated['effectiveDate'] ?? null,
        ]);

        return response()->json(['data' => $this->transform($page)], 201);
    }

    public function show(LegalPage $legalPage): JsonResponse
    {
        return response()->json(['data' => $this->transform($legalPage)]);
    }

    public function update(Request $request, LegalPage $legalPage): JsonResponse
    {
        $validated = $request->validate([
            'titleAr' => ['sometimes', 'string', 'max:255'],
            'titleEn' => ['sometimes', 'string', 'max:255'],
            'contentAr' => ['sometimes', 'string'],
            'contentEn' => ['sometimes', 'string'],
            'effectiveDate' => ['sometimes', 'nullable', 'date'],
        ]);

        $map = [
            'titleAr' => 'title_ar',
            'titleEn' => 'title_en',
            'contentAr' => 'content_ar',
            'contentEn' => 'content_en',
            'effectiveDate' => 'effective_date',
        ];

        $payload = [];
        foreach ($map as $input => $column) {
            if (array_key_exists($input, $validated)) {
                $payload[$column] = $validated[$input];
            }
        }

        $legalPage->update($payload);

        return response()->json(['data' => $this->transform($legalPage->fresh())]);
    }

    public function destroy(LegalPage $legalPage): JsonResponse
    {
        $legalPage->delete();

        return response()->json(['message' => 'Deleted']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(LegalPage $page): array
    {
        return [
            'id' => $page->id,
            'slug' => $page->slug,
            'titleAr' => $page->title_ar,
            'titleEn' => $page->title_en,
            'contentAr' => $page->content_ar,
            'contentEn' => $page->content_en,
            'effectiveDate' => $page->effective_date?->toDateString(),
            'updatedAt' => $page->updated_at?->toIso8601String(),
        ];
    }
}

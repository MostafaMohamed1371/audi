<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Models\PartnerCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PartnerCategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = PartnerCategory::query()->ordered();

        if ($search = $request->query('search')) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('title_ar', 'like', "%{$search}%")
                    ->orWhere('title_en', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $paginator = $query->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (PartnerCategory $category) => $this->transform($category))->values(),
            'meta' => [
                'page' => $paginator->currentPage(),
                'limit' => $paginator->perPage(),
                'total' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:120', Rule::unique('partner_categories', 'slug')],
            'titleAr' => ['required', 'string', 'max:255'],
            'titleEn' => ['required', 'string', 'max:255'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $category = PartnerCategory::query()->create([
            'slug' => $validated['slug'],
            'title_ar' => $validated['titleAr'],
            'title_en' => $validated['titleEn'],
            'sort_order' => $validated['sortOrder'] ?? 0,
        ]);

        return response()->json(['data' => $this->transform($category)], 201);
    }

    public function show(PartnerCategory $partnerCategory): JsonResponse
    {
        return response()->json(['data' => $this->transform($partnerCategory)]);
    }

    public function update(Request $request, PartnerCategory $partnerCategory): JsonResponse
    {
        $validated = $request->validate([
            'slug' => ['sometimes', 'string', 'max:120', Rule::unique('partner_categories', 'slug')->ignore($partnerCategory->id)],
            'titleAr' => ['sometimes', 'string', 'max:255'],
            'titleEn' => ['sometimes', 'string', 'max:255'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $payload = [];

        if (array_key_exists('slug', $validated)) {
            $payload['slug'] = $validated['slug'];
        }
        if (array_key_exists('titleAr', $validated)) {
            $payload['title_ar'] = $validated['titleAr'];
        }
        if (array_key_exists('titleEn', $validated)) {
            $payload['title_en'] = $validated['titleEn'];
        }
        if (array_key_exists('sortOrder', $validated)) {
            $payload['sort_order'] = $validated['sortOrder'];
        }

        $partnerCategory->update($payload);

        return response()->json(['data' => $this->transform($partnerCategory->fresh())]);
    }

    public function destroy(PartnerCategory $partnerCategory): JsonResponse
    {
        $partnerCategory->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(ReorderSortRequest $request): JsonResponse
    {
        foreach ($request->validated('items') as $item) {
            PartnerCategory::query()
                ->whereKey($item['id'])
                ->update(['sort_order' => $item['sortOrder']]);
        }

        return response()->json(['message' => 'Reordered']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(PartnerCategory $category): array
    {
        return [
            'id' => $category->id,
            'slug' => $category->slug,
            'titleAr' => $category->title_ar,
            'titleEn' => $category->title_en,
            'sortOrder' => $category->sort_order,
            'createdAt' => $category->created_at?->toIso8601String(),
            'updatedAt' => $category->updated_at?->toIso8601String(),
        ];
    }
}

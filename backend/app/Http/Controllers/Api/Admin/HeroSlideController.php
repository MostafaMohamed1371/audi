<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Models\HomeHeroSlide;
use App\Support\ImageUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HeroSlideController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = HomeHeroSlide::query()->ordered();

        if ($request->has('isActive')) {
            $query->where('is_active', filter_var($request->query('isActive'), FILTER_VALIDATE_BOOLEAN));
        }

        $paginator = $query->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (HomeHeroSlide $slide) => $this->transform($slide))->values(),
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
            'titleAr' => ['required', 'string', 'max:255'],
            'titleEn' => ['required', 'string', 'max:255'],
            'imageUrl' => ['nullable', 'string', 'max:500'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
            'isActive' => ['sometimes', 'boolean'],
        ]);

        $slide = HomeHeroSlide::query()->create([
            'title_ar' => $validated['titleAr'],
            'title_en' => $validated['titleEn'],
            'image_url' => $validated['imageUrl'] ?? null,
            'sort_order' => $validated['sortOrder'] ?? 0,
            'is_active' => $validated['isActive'] ?? true,
        ]);

        return response()->json(['data' => $this->transform($slide)], 201);
    }

    public function show(HomeHeroSlide $heroSlide): JsonResponse
    {
        return response()->json(['data' => $this->transform($heroSlide)]);
    }

    public function update(Request $request, HomeHeroSlide $heroSlide): JsonResponse
    {
        $validated = $request->validate([
            'titleAr' => ['sometimes', 'string', 'max:255'],
            'titleEn' => ['sometimes', 'string', 'max:255'],
            'imageUrl' => ['nullable', 'string', 'max:500'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
            'isActive' => ['sometimes', 'boolean'],
        ]);

        $payload = [];

        if (array_key_exists('titleAr', $validated)) {
            $payload['title_ar'] = $validated['titleAr'];
        }
        if (array_key_exists('titleEn', $validated)) {
            $payload['title_en'] = $validated['titleEn'];
        }
        if (array_key_exists('imageUrl', $validated)) {
            $payload['image_url'] = $validated['imageUrl'];
        }
        if (array_key_exists('sortOrder', $validated)) {
            $payload['sort_order'] = $validated['sortOrder'];
        }
        if (array_key_exists('isActive', $validated)) {
            $payload['is_active'] = $validated['isActive'];
        }

        $heroSlide->update($payload);

        return response()->json(['data' => $this->transform($heroSlide->fresh())]);
    }

    public function destroy(HomeHeroSlide $heroSlide): JsonResponse
    {
        $heroSlide->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(ReorderSortRequest $request): JsonResponse
    {
        foreach ($request->validated('items') as $item) {
            HomeHeroSlide::query()
                ->whereKey($item['id'])
                ->update(['sort_order' => $item['sortOrder']]);
        }

        return response()->json(['message' => 'Reordered']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(HomeHeroSlide $slide): array
    {
        return [
            'id' => $slide->id,
            'titleAr' => $slide->title_ar,
            'titleEn' => $slide->title_en,
            'imageUrl' => ImageUrl::api($slide->image_url),
            'sortOrder' => $slide->sort_order,
            'isActive' => $slide->is_active,
        ];
    }
}

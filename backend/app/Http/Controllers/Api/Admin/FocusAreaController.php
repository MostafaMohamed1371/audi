<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Models\FocusArea;
use App\Support\ImageUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FocusAreaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = FocusArea::query()->ordered();

        if ($request->has('isPublished')) {
            $query->where('is_published', filter_var($request->query('isPublished'), FILTER_VALIDATE_BOOLEAN));
        }

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
            'data' => $paginator->getCollection()->map(fn (FocusArea $area) => $this->transform($area))->values(),
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
            'slug' => ['required', 'string', 'max:120', Rule::unique('focus_areas', 'slug')],
            'number' => ['required', 'string', 'max:4'],
            'titleAr' => ['required', 'string', 'max:255'],
            'titleEn' => ['required', 'string', 'max:255'],
            'highlightAr' => ['required', 'string', 'max:255'],
            'highlightEn' => ['required', 'string', 'max:255'],
            'tagsAr' => ['required', 'array'],
            'tagsEn' => ['required', 'array'],
            'descriptionAr' => ['required', 'string'],
            'descriptionEn' => ['required', 'string'],
            'listImageUrl' => ['nullable', 'string', 'max:500'],
            'detailImageUrl' => ['nullable', 'string', 'max:500'],
            'isPublished' => ['sometimes', 'boolean'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $area = FocusArea::query()->create([
            'slug' => $validated['slug'],
            'number' => $validated['number'],
            'title_ar' => $validated['titleAr'],
            'title_en' => $validated['titleEn'],
            'highlight_ar' => $validated['highlightAr'],
            'highlight_en' => $validated['highlightEn'],
            'tags_ar' => $validated['tagsAr'],
            'tags_en' => $validated['tagsEn'],
            'description_ar' => $validated['descriptionAr'],
            'description_en' => $validated['descriptionEn'],
            'list_image_url' => $validated['listImageUrl'] ?? null,
            'detail_image_url' => $validated['detailImageUrl'] ?? null,
            'is_published' => $validated['isPublished'] ?? true,
            'sort_order' => $validated['sortOrder'] ?? 0,
        ]);

        return response()->json(['data' => $this->transform($area)], 201);
    }

    public function show(FocusArea $focusArea): JsonResponse
    {
        return response()->json(['data' => $this->transform($focusArea)]);
    }

    public function update(Request $request, FocusArea $focusArea): JsonResponse
    {
        $validated = $request->validate([
            'slug' => ['sometimes', 'string', 'max:120', Rule::unique('focus_areas', 'slug')->ignore($focusArea->id)],
            'number' => ['sometimes', 'string', 'max:4'],
            'titleAr' => ['sometimes', 'string', 'max:255'],
            'titleEn' => ['sometimes', 'string', 'max:255'],
            'highlightAr' => ['sometimes', 'string', 'max:255'],
            'highlightEn' => ['sometimes', 'string', 'max:255'],
            'tagsAr' => ['sometimes', 'array'],
            'tagsEn' => ['sometimes', 'array'],
            'descriptionAr' => ['sometimes', 'string'],
            'descriptionEn' => ['sometimes', 'string'],
            'listImageUrl' => ['sometimes', 'nullable', 'string', 'max:500'],
            'detailImageUrl' => ['sometimes', 'nullable', 'string', 'max:500'],
            'isPublished' => ['sometimes', 'boolean'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $payload = [];

        if (array_key_exists('slug', $validated)) {
            $payload['slug'] = $validated['slug'];
        }
        if (array_key_exists('number', $validated)) {
            $payload['number'] = $validated['number'];
        }
        if (array_key_exists('titleAr', $validated)) {
            $payload['title_ar'] = $validated['titleAr'];
        }
        if (array_key_exists('titleEn', $validated)) {
            $payload['title_en'] = $validated['titleEn'];
        }
        if (array_key_exists('highlightAr', $validated)) {
            $payload['highlight_ar'] = $validated['highlightAr'];
        }
        if (array_key_exists('highlightEn', $validated)) {
            $payload['highlight_en'] = $validated['highlightEn'];
        }
        if (array_key_exists('tagsAr', $validated)) {
            $payload['tags_ar'] = $validated['tagsAr'];
        }
        if (array_key_exists('tagsEn', $validated)) {
            $payload['tags_en'] = $validated['tagsEn'];
        }
        if (array_key_exists('descriptionAr', $validated)) {
            $payload['description_ar'] = $validated['descriptionAr'];
        }
        if (array_key_exists('descriptionEn', $validated)) {
            $payload['description_en'] = $validated['descriptionEn'];
        }
        if (array_key_exists('listImageUrl', $validated)) {
            $payload['list_image_url'] = $validated['listImageUrl'];
        }
        if (array_key_exists('detailImageUrl', $validated)) {
            $payload['detail_image_url'] = $validated['detailImageUrl'];
        }
        if (array_key_exists('isPublished', $validated)) {
            $payload['is_published'] = $validated['isPublished'];
        }
        if (array_key_exists('sortOrder', $validated)) {
            $payload['sort_order'] = $validated['sortOrder'];
        }

        $focusArea->update($payload);

        return response()->json(['data' => $this->transform($focusArea->fresh())]);
    }

    public function destroy(FocusArea $focusArea): JsonResponse
    {
        $focusArea->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(ReorderSortRequest $request): JsonResponse
    {
        foreach ($request->validated('items') as $item) {
            FocusArea::query()
                ->whereKey($item['id'])
                ->update(['sort_order' => $item['sortOrder']]);
        }

        return response()->json(['message' => 'Reordered']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(FocusArea $area): array
    {
        return [
            'id' => $area->id,
            'slug' => $area->slug,
            'number' => $area->number,
            'titleAr' => $area->title_ar,
            'titleEn' => $area->title_en,
            'highlightAr' => $area->highlight_ar,
            'highlightEn' => $area->highlight_en,
            'tagsAr' => $area->tags_ar,
            'tagsEn' => $area->tags_en,
            'descriptionAr' => $area->description_ar,
            'descriptionEn' => $area->description_en,
            'listImageUrl' => ImageUrl::api($area->list_image_url),
            'detailImageUrl' => ImageUrl::api($area->detail_image_url),
            'isPublished' => $area->is_published,
            'sortOrder' => $area->sort_order,
            'createdAt' => $area->created_at?->toIso8601String(),
            'updatedAt' => $area->updated_at?->toIso8601String(),
        ];
    }
}

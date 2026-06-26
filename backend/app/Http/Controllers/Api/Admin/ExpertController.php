<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Models\Expert;
use App\Support\ImageUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpertController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $paginator = Expert::query()->ordered()->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (Expert $expert) => $this->transform($expert))->values(),
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
            'nameAr' => ['required', 'string', 'max:255'],
            'nameEn' => ['required', 'string', 'max:255'],
            'specialtyAr' => ['required', 'string', 'max:255'],
            'specialtyEn' => ['required', 'string', 'max:255'],
            'imageUrl' => ['nullable', 'string', 'max:500'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $expert = Expert::query()->create([
            'name_ar' => $validated['nameAr'],
            'name_en' => $validated['nameEn'],
            'specialty_ar' => $validated['specialtyAr'],
            'specialty_en' => $validated['specialtyEn'],
            'image_url' => $validated['imageUrl'] ?? null,
            'sort_order' => $validated['sortOrder'] ?? 0,
        ]);

        return response()->json(['data' => $this->transform($expert)], 201);
    }

    public function show(Expert $expert): JsonResponse
    {
        return response()->json(['data' => $this->transform($expert)]);
    }

    public function update(Request $request, Expert $expert): JsonResponse
    {
        $validated = $request->validate([
            'nameAr' => ['sometimes', 'string', 'max:255'],
            'nameEn' => ['sometimes', 'string', 'max:255'],
            'specialtyAr' => ['sometimes', 'string', 'max:255'],
            'specialtyEn' => ['sometimes', 'string', 'max:255'],
            'imageUrl' => ['sometimes', 'nullable', 'string', 'max:500'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $payload = [];

        if (array_key_exists('nameAr', $validated)) {
            $payload['name_ar'] = $validated['nameAr'];
        }
        if (array_key_exists('nameEn', $validated)) {
            $payload['name_en'] = $validated['nameEn'];
        }
        if (array_key_exists('specialtyAr', $validated)) {
            $payload['specialty_ar'] = $validated['specialtyAr'];
        }
        if (array_key_exists('specialtyEn', $validated)) {
            $payload['specialty_en'] = $validated['specialtyEn'];
        }
        if (array_key_exists('imageUrl', $validated)) {
            $payload['image_url'] = $validated['imageUrl'];
        }
        if (array_key_exists('sortOrder', $validated)) {
            $payload['sort_order'] = $validated['sortOrder'];
        }

        $expert->update($payload);

        return response()->json(['data' => $this->transform($expert->fresh())]);
    }

    public function destroy(Expert $expert): JsonResponse
    {
        $expert->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(ReorderSortRequest $request): JsonResponse
    {
        foreach ($request->validated('items') as $item) {
            Expert::query()
                ->whereKey($item['id'])
                ->update(['sort_order' => $item['sortOrder']]);
        }

        return response()->json(['message' => 'Reordered']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(Expert $expert): array
    {
        return [
            'id' => $expert->id,
            'nameAr' => $expert->name_ar,
            'nameEn' => $expert->name_en,
            'specialtyAr' => $expert->specialty_ar,
            'specialtyEn' => $expert->specialty_en,
            'imageUrl' => ImageUrl::api($expert->image_url),
            'sortOrder' => $expert->sort_order,
            'createdAt' => $expert->created_at?->toIso8601String(),
            'updatedAt' => $expert->updated_at?->toIso8601String(),
        ];
    }
}

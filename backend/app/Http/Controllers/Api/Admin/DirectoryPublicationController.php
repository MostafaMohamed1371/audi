<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Models\DirectoryPublication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DirectoryPublicationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $paginator = DirectoryPublication::query()->ordered()->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (DirectoryPublication $publication) => $this->transform($publication))->values(),
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
            'number' => ['required', 'string', 'max:10'],
            'nameAr' => ['required', 'string', 'max:255'],
            'nameEn' => ['required', 'string', 'max:255'],
            'descriptionAr' => ['nullable', 'string'],
            'descriptionEn' => ['nullable', 'string'],
            'detailAr' => ['nullable', 'array'],
            'detailEn' => ['nullable', 'array'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $publication = DirectoryPublication::query()->create([
            'number' => $validated['number'],
            'name_ar' => $validated['nameAr'],
            'name_en' => $validated['nameEn'],
            'description_ar' => $validated['descriptionAr'] ?? null,
            'description_en' => $validated['descriptionEn'] ?? null,
            'detail_ar' => $validated['detailAr'] ?? null,
            'detail_en' => $validated['detailEn'] ?? null,
            'sort_order' => $validated['sortOrder'] ?? 0,
        ]);

        return response()->json(['data' => $this->transform($publication)], 201);
    }

    public function show(DirectoryPublication $directoryPublication): JsonResponse
    {
        return response()->json(['data' => $this->transform($directoryPublication)]);
    }

    public function update(Request $request, DirectoryPublication $directoryPublication): JsonResponse
    {
        $validated = $request->validate([
            'number' => ['sometimes', 'string', 'max:10'],
            'nameAr' => ['sometimes', 'string', 'max:255'],
            'nameEn' => ['sometimes', 'string', 'max:255'],
            'descriptionAr' => ['sometimes', 'nullable', 'string'],
            'descriptionEn' => ['sometimes', 'nullable', 'string'],
            'detailAr' => ['sometimes', 'nullable', 'array'],
            'detailEn' => ['sometimes', 'nullable', 'array'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $payload = [];

        if (array_key_exists('number', $validated)) {
            $payload['number'] = $validated['number'];
        }
        if (array_key_exists('nameAr', $validated)) {
            $payload['name_ar'] = $validated['nameAr'];
        }
        if (array_key_exists('nameEn', $validated)) {
            $payload['name_en'] = $validated['nameEn'];
        }
        if (array_key_exists('descriptionAr', $validated)) {
            $payload['description_ar'] = $validated['descriptionAr'];
        }
        if (array_key_exists('descriptionEn', $validated)) {
            $payload['description_en'] = $validated['descriptionEn'];
        }
        if (array_key_exists('detailAr', $validated)) {
            $payload['detail_ar'] = $validated['detailAr'];
        }
        if (array_key_exists('detailEn', $validated)) {
            $payload['detail_en'] = $validated['detailEn'];
        }
        if (array_key_exists('sortOrder', $validated)) {
            $payload['sort_order'] = $validated['sortOrder'];
        }

        $directoryPublication->update($payload);

        return response()->json(['data' => $this->transform($directoryPublication->fresh())]);
    }

    public function destroy(DirectoryPublication $directoryPublication): JsonResponse
    {
        $directoryPublication->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(ReorderSortRequest $request): JsonResponse
    {
        foreach ($request->validated('items') as $item) {
            DirectoryPublication::query()
                ->whereKey($item['id'])
                ->update(['sort_order' => $item['sortOrder']]);
        }

        return response()->json(['message' => 'Reordered']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(DirectoryPublication $publication): array
    {
        return [
            'id' => $publication->id,
            'number' => $publication->number,
            'nameAr' => $publication->name_ar,
            'nameEn' => $publication->name_en,
            'descriptionAr' => $publication->description_ar,
            'descriptionEn' => $publication->description_en,
            'detailAr' => $publication->detail_ar,
            'detailEn' => $publication->detail_en,
            'sortOrder' => $publication->sort_order,
            'createdAt' => $publication->created_at?->toIso8601String(),
            'updatedAt' => $publication->updated_at?->toIso8601String(),
        ];
    }
}

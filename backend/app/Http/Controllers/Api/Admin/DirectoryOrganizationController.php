<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Models\DirectoryOrganization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DirectoryOrganizationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $paginator = DirectoryOrganization::query()->ordered()->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (DirectoryOrganization $organization) => $this->transform($organization))->values(),
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
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $organization = DirectoryOrganization::query()->create([
            'number' => $validated['number'],
            'name_ar' => $validated['nameAr'],
            'name_en' => $validated['nameEn'],
            'description_ar' => $validated['descriptionAr'] ?? null,
            'description_en' => $validated['descriptionEn'] ?? null,
            'sort_order' => $validated['sortOrder'] ?? 0,
        ]);

        return response()->json(['data' => $this->transform($organization)], 201);
    }

    public function show(DirectoryOrganization $directoryOrganization): JsonResponse
    {
        return response()->json(['data' => $this->transform($directoryOrganization)]);
    }

    public function update(Request $request, DirectoryOrganization $directoryOrganization): JsonResponse
    {
        $validated = $request->validate([
            'number' => ['sometimes', 'string', 'max:10'],
            'nameAr' => ['sometimes', 'string', 'max:255'],
            'nameEn' => ['sometimes', 'string', 'max:255'],
            'descriptionAr' => ['sometimes', 'nullable', 'string'],
            'descriptionEn' => ['sometimes', 'nullable', 'string'],
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
        if (array_key_exists('sortOrder', $validated)) {
            $payload['sort_order'] = $validated['sortOrder'];
        }

        $directoryOrganization->update($payload);

        return response()->json(['data' => $this->transform($directoryOrganization->fresh())]);
    }

    public function destroy(DirectoryOrganization $directoryOrganization): JsonResponse
    {
        $directoryOrganization->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(ReorderSortRequest $request): JsonResponse
    {
        foreach ($request->validated('items') as $item) {
            DirectoryOrganization::query()
                ->whereKey($item['id'])
                ->update(['sort_order' => $item['sortOrder']]);
        }

        return response()->json(['message' => 'Reordered']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(DirectoryOrganization $organization): array
    {
        return [
            'id' => $organization->id,
            'number' => $organization->number,
            'nameAr' => $organization->name_ar,
            'nameEn' => $organization->name_en,
            'descriptionAr' => $organization->description_ar,
            'descriptionEn' => $organization->description_en,
            'sortOrder' => $organization->sort_order,
            'createdAt' => $organization->created_at?->toIso8601String(),
            'updatedAt' => $organization->updated_at?->toIso8601String(),
        ];
    }
}

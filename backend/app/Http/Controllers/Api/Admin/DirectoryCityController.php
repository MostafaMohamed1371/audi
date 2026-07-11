<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Models\DirectoryCity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DirectoryCityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = DirectoryCity::query()->ordered();

        if ($countryCode = $request->query('countryCode', $request->query('country_code'))) {
            $query->where('country_code', strtoupper((string) $countryCode));
        }

        if ($citySize = $request->query('citySize', $request->query('city_size'))) {
            $query->where('city_size', $citySize);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name_ar', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('description_ar', 'like', "%{$search}%")
                    ->orWhere('description_en', 'like', "%{$search}%");
            });
        }

        $paginator = $query->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (DirectoryCity $city) => $this->transform($city))->values(),
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
            'countryCode' => ['nullable', 'string', 'size:2'],
            'citySize' => ['nullable', 'string', 'max:50'],
            'detailAr' => ['nullable', 'array'],
            'detailEn' => ['nullable', 'array'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $city = DirectoryCity::query()->create([
            'number' => $validated['number'],
            'name_ar' => $validated['nameAr'],
            'name_en' => $validated['nameEn'],
            'description_ar' => $validated['descriptionAr'] ?? null,
            'description_en' => $validated['descriptionEn'] ?? null,
            'country_code' => isset($validated['countryCode']) ? strtoupper($validated['countryCode']) : null,
            'city_size' => $validated['citySize'] ?? null,
            'detail_ar' => $validated['detailAr'] ?? null,
            'detail_en' => $validated['detailEn'] ?? null,
            'sort_order' => $validated['sortOrder'] ?? 0,
        ]);

        return response()->json(['data' => $this->transform($city)], 201);
    }

    public function show(DirectoryCity $directoryCity): JsonResponse
    {
        return response()->json(['data' => $this->transform($directoryCity)]);
    }

    public function update(Request $request, DirectoryCity $directoryCity): JsonResponse
    {
        $validated = $request->validate([
            'number' => ['sometimes', 'string', 'max:10'],
            'nameAr' => ['sometimes', 'string', 'max:255'],
            'nameEn' => ['sometimes', 'string', 'max:255'],
            'descriptionAr' => ['sometimes', 'nullable', 'string'],
            'descriptionEn' => ['sometimes', 'nullable', 'string'],
            'countryCode' => ['sometimes', 'nullable', 'string', 'size:2'],
            'citySize' => ['sometimes', 'nullable', 'string', 'max:50'],
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
        if (array_key_exists('countryCode', $validated)) {
            $payload['country_code'] = $validated['countryCode'] !== null
                ? strtoupper($validated['countryCode'])
                : null;
        }
        if (array_key_exists('citySize', $validated)) {
            $payload['city_size'] = $validated['citySize'];
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

        $directoryCity->update($payload);

        return response()->json(['data' => $this->transform($directoryCity->fresh())]);
    }

    public function destroy(DirectoryCity $directoryCity): JsonResponse
    {
        $directoryCity->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(ReorderSortRequest $request): JsonResponse
    {
        foreach ($request->validated('items') as $item) {
            DirectoryCity::query()
                ->whereKey($item['id'])
                ->update(['sort_order' => $item['sortOrder']]);
        }

        return response()->json(['message' => 'Reordered']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(DirectoryCity $city): array
    {
        return [
            'id' => $city->id,
            'number' => $city->number,
            'nameAr' => $city->name_ar,
            'nameEn' => $city->name_en,
            'descriptionAr' => $city->description_ar,
            'descriptionEn' => $city->description_en,
            'countryCode' => $city->country_code,
            'citySize' => $city->city_size,
            'detailAr' => $city->detail_ar,
            'detailEn' => $city->detail_en,
            'sortOrder' => $city->sort_order,
            'createdAt' => $city->created_at?->toIso8601String(),
            'updatedAt' => $city->updated_at?->toIso8601String(),
        ];
    }
}

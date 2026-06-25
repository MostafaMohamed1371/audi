<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PartnerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = Partner::query()->ordered();

        if ($partnerCategoryId = $request->query('partnerCategoryId', $request->query('partner_category_id'))) {
            $query->where('partner_category_id', $partnerCategoryId);
        }

        if ($request->has('isFeatured')) {
            $query->where('is_featured', filter_var($request->query('isFeatured'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($search = $request->query('search')) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name_ar', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        $paginator = $query->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (Partner $partner) => $this->transform($partner))->values(),
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
            'partnerCategoryId' => ['nullable', 'integer', Rule::exists('partner_categories', 'id')],
            'nameAr' => ['required', 'string', 'max:255'],
            'nameEn' => ['required', 'string', 'max:255'],
            'logoUrl' => ['nullable', 'string', 'max:500'],
            'isFeatured' => ['sometimes', 'boolean'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $partner = Partner::query()->create([
            'partner_category_id' => $validated['partnerCategoryId'] ?? null,
            'name_ar' => $validated['nameAr'],
            'name_en' => $validated['nameEn'],
            'logo_url' => $validated['logoUrl'] ?? null,
            'is_featured' => $validated['isFeatured'] ?? false,
            'sort_order' => $validated['sortOrder'] ?? 0,
        ]);

        return response()->json(['data' => $this->transform($partner)], 201);
    }

    public function show(Partner $partner): JsonResponse
    {
        return response()->json(['data' => $this->transform($partner)]);
    }

    public function update(Request $request, Partner $partner): JsonResponse
    {
        $validated = $request->validate([
            'partnerCategoryId' => ['sometimes', 'nullable', 'integer', Rule::exists('partner_categories', 'id')],
            'nameAr' => ['sometimes', 'string', 'max:255'],
            'nameEn' => ['sometimes', 'string', 'max:255'],
            'logoUrl' => ['sometimes', 'nullable', 'string', 'max:500'],
            'isFeatured' => ['sometimes', 'boolean'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $payload = [];

        if (array_key_exists('partnerCategoryId', $validated)) {
            $payload['partner_category_id'] = $validated['partnerCategoryId'];
        }
        if (array_key_exists('nameAr', $validated)) {
            $payload['name_ar'] = $validated['nameAr'];
        }
        if (array_key_exists('nameEn', $validated)) {
            $payload['name_en'] = $validated['nameEn'];
        }
        if (array_key_exists('logoUrl', $validated)) {
            $payload['logo_url'] = $validated['logoUrl'];
        }
        if (array_key_exists('isFeatured', $validated)) {
            $payload['is_featured'] = $validated['isFeatured'];
        }
        if (array_key_exists('sortOrder', $validated)) {
            $payload['sort_order'] = $validated['sortOrder'];
        }

        $partner->update($payload);

        return response()->json(['data' => $this->transform($partner->fresh())]);
    }

    public function destroy(Partner $partner): JsonResponse
    {
        $partner->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(ReorderSortRequest $request): JsonResponse
    {
        foreach ($request->validated('items') as $item) {
            Partner::query()
                ->whereKey($item['id'])
                ->update(['sort_order' => $item['sortOrder']]);
        }

        return response()->json(['message' => 'Reordered']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(Partner $partner): array
    {
        return [
            'id' => $partner->id,
            'partnerCategoryId' => $partner->partner_category_id,
            'nameAr' => $partner->name_ar,
            'nameEn' => $partner->name_en,
            'logoUrl' => $partner->logo_url,
            'isFeatured' => $partner->is_featured,
            'sortOrder' => $partner->sort_order,
            'createdAt' => $partner->created_at?->toIso8601String(),
            'updatedAt' => $partner->updated_at?->toIso8601String(),
        ];
    }
}

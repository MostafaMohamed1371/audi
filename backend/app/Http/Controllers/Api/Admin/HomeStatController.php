<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Models\HomeStat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeStatController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $paginator = HomeStat::query()->ordered()->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (HomeStat $stat) => $this->transform($stat))->values(),
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
            'value' => ['required', 'string', 'max:50'],
            'labelAr' => ['required', 'string', 'max:255'],
            'labelEn' => ['required', 'string', 'max:255'],
            'descriptionAr' => ['required', 'string', 'max:255'],
            'descriptionEn' => ['required', 'string', 'max:255'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $stat = HomeStat::query()->create([
            'value' => $validated['value'],
            'label_ar' => $validated['labelAr'],
            'label_en' => $validated['labelEn'],
            'description_ar' => $validated['descriptionAr'],
            'description_en' => $validated['descriptionEn'],
            'sort_order' => $validated['sortOrder'] ?? 0,
        ]);

        return response()->json(['data' => $this->transform($stat)], 201);
    }

    public function show(HomeStat $homeStat): JsonResponse
    {
        return response()->json(['data' => $this->transform($homeStat)]);
    }

    public function update(Request $request, HomeStat $homeStat): JsonResponse
    {
        $validated = $request->validate([
            'value' => ['sometimes', 'string', 'max:50'],
            'labelAr' => ['sometimes', 'string', 'max:255'],
            'labelEn' => ['sometimes', 'string', 'max:255'],
            'descriptionAr' => ['sometimes', 'string', 'max:255'],
            'descriptionEn' => ['sometimes', 'string', 'max:255'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $payload = [];

        if (array_key_exists('value', $validated)) {
            $payload['value'] = $validated['value'];
        }
        if (array_key_exists('labelAr', $validated)) {
            $payload['label_ar'] = $validated['labelAr'];
        }
        if (array_key_exists('labelEn', $validated)) {
            $payload['label_en'] = $validated['labelEn'];
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

        $homeStat->update($payload);

        return response()->json(['data' => $this->transform($homeStat->fresh())]);
    }

    public function destroy(HomeStat $homeStat): JsonResponse
    {
        $homeStat->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(ReorderSortRequest $request): JsonResponse
    {
        foreach ($request->validated('items') as $item) {
            HomeStat::query()
                ->whereKey($item['id'])
                ->update(['sort_order' => $item['sortOrder']]);
        }

        return response()->json(['message' => 'Reordered']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(HomeStat $stat): array
    {
        return [
            'id' => $stat->id,
            'value' => $stat->value,
            'labelAr' => $stat->label_ar,
            'labelEn' => $stat->label_en,
            'descriptionAr' => $stat->description_ar,
            'descriptionEn' => $stat->description_en,
            'sortOrder' => $stat->sort_order,
        ];
    }
}

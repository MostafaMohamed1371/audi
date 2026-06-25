<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Models\StrategyPillar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StrategyPillarController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $paginator = StrategyPillar::query()->ordered()->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (StrategyPillar $pillar) => $this->transform($pillar))->values(),
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
            'number' => ['required', 'string', 'max:4'],
            'textAr' => ['required', 'string'],
            'textEn' => ['required', 'string'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $pillar = StrategyPillar::query()->create([
            'number' => $validated['number'],
            'text_ar' => $validated['textAr'],
            'text_en' => $validated['textEn'],
            'sort_order' => $validated['sortOrder'] ?? 0,
        ]);

        return response()->json(['data' => $this->transform($pillar)], 201);
    }

    public function show(StrategyPillar $strategyPillar): JsonResponse
    {
        return response()->json(['data' => $this->transform($strategyPillar)]);
    }

    public function update(Request $request, StrategyPillar $strategyPillar): JsonResponse
    {
        $validated = $request->validate([
            'number' => ['sometimes', 'string', 'max:4'],
            'textAr' => ['sometimes', 'string'],
            'textEn' => ['sometimes', 'string'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $payload = [];

        if (array_key_exists('number', $validated)) {
            $payload['number'] = $validated['number'];
        }
        if (array_key_exists('textAr', $validated)) {
            $payload['text_ar'] = $validated['textAr'];
        }
        if (array_key_exists('textEn', $validated)) {
            $payload['text_en'] = $validated['textEn'];
        }
        if (array_key_exists('sortOrder', $validated)) {
            $payload['sort_order'] = $validated['sortOrder'];
        }

        $strategyPillar->update($payload);

        return response()->json(['data' => $this->transform($strategyPillar->fresh())]);
    }

    public function destroy(StrategyPillar $strategyPillar): JsonResponse
    {
        $strategyPillar->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(ReorderSortRequest $request): JsonResponse
    {
        foreach ($request->validated('items') as $item) {
            StrategyPillar::query()
                ->whereKey($item['id'])
                ->update(['sort_order' => $item['sortOrder']]);
        }

        return response()->json(['message' => 'Reordered']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(StrategyPillar $pillar): array
    {
        return [
            'id' => $pillar->id,
            'number' => $pillar->number,
            'textAr' => $pillar->text_ar,
            'textEn' => $pillar->text_en,
            'sortOrder' => $pillar->sort_order,
            'createdAt' => $pillar->created_at?->toIso8601String(),
            'updatedAt' => $pillar->updated_at?->toIso8601String(),
        ];
    }
}

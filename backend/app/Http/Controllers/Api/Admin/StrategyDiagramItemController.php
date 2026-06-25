<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Models\StrategyDiagramItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StrategyDiagramItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = StrategyDiagramItem::query()->ordered();

        if ($search = $request->query('search')) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('item_key', 'like', "%{$search}%")
                    ->orWhere('title_ar', 'like', "%{$search}%")
                    ->orWhere('title_en', 'like', "%{$search}%");
            });
        }

        $paginator = $query->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (StrategyDiagramItem $item) => $this->transform($item))->values(),
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
            'itemKey' => ['required', 'string', 'max:120', Rule::unique('strategy_diagram_items', 'item_key')],
            'titleAr' => ['required', 'string', 'max:255'],
            'titleEn' => ['required', 'string', 'max:255'],
            'contentAr' => ['nullable', 'string'],
            'contentEn' => ['nullable', 'string'],
            'columnsAr' => ['nullable', 'array'],
            'columnsEn' => ['nullable', 'array'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $item = StrategyDiagramItem::query()->create([
            'item_key' => $validated['itemKey'],
            'title_ar' => $validated['titleAr'],
            'title_en' => $validated['titleEn'],
            'content_ar' => $validated['contentAr'] ?? null,
            'content_en' => $validated['contentEn'] ?? null,
            'columns_ar' => $validated['columnsAr'] ?? null,
            'columns_en' => $validated['columnsEn'] ?? null,
            'sort_order' => $validated['sortOrder'] ?? 0,
        ]);

        return response()->json(['data' => $this->transform($item)], 201);
    }

    public function show(StrategyDiagramItem $strategyDiagramItem): JsonResponse
    {
        return response()->json(['data' => $this->transform($strategyDiagramItem)]);
    }

    public function update(Request $request, StrategyDiagramItem $strategyDiagramItem): JsonResponse
    {
        $validated = $request->validate([
            'itemKey' => ['sometimes', 'string', 'max:120', Rule::unique('strategy_diagram_items', 'item_key')->ignore($strategyDiagramItem->id)],
            'titleAr' => ['sometimes', 'string', 'max:255'],
            'titleEn' => ['sometimes', 'string', 'max:255'],
            'contentAr' => ['sometimes', 'nullable', 'string'],
            'contentEn' => ['sometimes', 'nullable', 'string'],
            'columnsAr' => ['sometimes', 'nullable', 'array'],
            'columnsEn' => ['sometimes', 'nullable', 'array'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $payload = [];

        if (array_key_exists('itemKey', $validated)) {
            $payload['item_key'] = $validated['itemKey'];
        }
        if (array_key_exists('titleAr', $validated)) {
            $payload['title_ar'] = $validated['titleAr'];
        }
        if (array_key_exists('titleEn', $validated)) {
            $payload['title_en'] = $validated['titleEn'];
        }
        if (array_key_exists('contentAr', $validated)) {
            $payload['content_ar'] = $validated['contentAr'];
        }
        if (array_key_exists('contentEn', $validated)) {
            $payload['content_en'] = $validated['contentEn'];
        }
        if (array_key_exists('columnsAr', $validated)) {
            $payload['columns_ar'] = $validated['columnsAr'];
        }
        if (array_key_exists('columnsEn', $validated)) {
            $payload['columns_en'] = $validated['columnsEn'];
        }
        if (array_key_exists('sortOrder', $validated)) {
            $payload['sort_order'] = $validated['sortOrder'];
        }

        $strategyDiagramItem->update($payload);

        return response()->json(['data' => $this->transform($strategyDiagramItem->fresh())]);
    }

    public function destroy(StrategyDiagramItem $strategyDiagramItem): JsonResponse
    {
        $strategyDiagramItem->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(ReorderSortRequest $request): JsonResponse
    {
        foreach ($request->validated('items') as $item) {
            StrategyDiagramItem::query()
                ->whereKey($item['id'])
                ->update(['sort_order' => $item['sortOrder']]);
        }

        return response()->json(['message' => 'Reordered']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(StrategyDiagramItem $item): array
    {
        return [
            'id' => $item->id,
            'itemKey' => $item->item_key,
            'titleAr' => $item->title_ar,
            'titleEn' => $item->title_en,
            'contentAr' => $item->content_ar,
            'contentEn' => $item->content_en,
            'columnsAr' => $item->columns_ar,
            'columnsEn' => $item->columns_en,
            'sortOrder' => $item->sort_order,
            'createdAt' => $item->created_at?->toIso8601String(),
            'updatedAt' => $item->updated_at?->toIso8601String(),
        ];
    }
}

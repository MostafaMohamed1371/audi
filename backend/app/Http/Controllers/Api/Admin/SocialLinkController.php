<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Models\SocialLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SocialLinkController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);
        $paginator = SocialLink::query()->ordered()->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (SocialLink $link) => $this->transform($link))->values(),
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
            'platform' => ['required', 'string', 'max:60'],
            'url' => ['required', 'string', 'max:500', 'url'],
            'icon' => ['nullable', 'string', 'max:60'],
            'isActive' => ['sometimes', 'boolean'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $link = SocialLink::query()->create([
            'platform' => $validated['platform'],
            'url' => $validated['url'],
            'icon' => $validated['icon'] ?? $validated['platform'],
            'is_active' => $validated['isActive'] ?? true,
            'sort_order' => $validated['sortOrder'] ?? 0,
        ]);

        return response()->json(['data' => $this->transform($link)], 201);
    }

    public function show(SocialLink $socialLink): JsonResponse
    {
        return response()->json(['data' => $this->transform($socialLink)]);
    }

    public function update(Request $request, SocialLink $socialLink): JsonResponse
    {
        $validated = $request->validate([
            'platform' => ['sometimes', 'string', 'max:60'],
            'url' => ['sometimes', 'string', 'max:500', 'url'],
            'icon' => ['sometimes', 'nullable', 'string', 'max:60'],
            'isActive' => ['sometimes', 'boolean'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $map = [
            'platform' => 'platform',
            'url' => 'url',
            'icon' => 'icon',
            'isActive' => 'is_active',
            'sortOrder' => 'sort_order',
        ];

        $payload = [];
        foreach ($map as $input => $column) {
            if (array_key_exists($input, $validated)) {
                $payload[$column] = $validated[$input];
            }
        }

        $socialLink->update($payload);

        return response()->json(['data' => $this->transform($socialLink->fresh())]);
    }

    public function destroy(SocialLink $socialLink): JsonResponse
    {
        $socialLink->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(ReorderSortRequest $request): JsonResponse
    {
        foreach ($request->validated('items') as $item) {
            SocialLink::query()
                ->whereKey($item['id'])
                ->update(['sort_order' => $item['sortOrder']]);
        }

        return response()->json(['message' => 'Reordered']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(SocialLink $link): array
    {
        return [
            'id' => $link->id,
            'platform' => $link->platform,
            'url' => $link->url,
            'icon' => $link->icon,
            'isActive' => $link->is_active,
            'sortOrder' => $link->sort_order,
            'createdAt' => $link->created_at?->toIso8601String(),
            'updatedAt' => $link->updated_at?->toIso8601String(),
        ];
    }
}

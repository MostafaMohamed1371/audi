<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Http\Requests\Api\Admin\StoreResourceRequest;
use App\Http\Requests\Api\Admin\UpdateResourceRequest;
use App\Http\Resources\Api\Admin\ResourceResource;
use App\Models\Resource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ResourceController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = Resource::query()
            ->orderByDesc('published_date')
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($type = $request->query('resourceType', $request->query('type'))) {
            $query->where('resource_type', $type);
        }

        if ($categoryId = $request->query('knowledgeCategoryId', $request->query('knowledge_category_id'))) {
            $query->where('knowledge_category_id', $categoryId);
        }

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

        return ResourceResource::collection($paginator)->additional([
            'meta' => [
                'page' => $paginator->currentPage(),
                'limit' => $paginator->perPage(),
                'total' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(StoreResourceRequest $request): JsonResponse
    {
        $resource = Resource::query()->create($request->validatedPayload());

        return (new ResourceResource($resource))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Resource $resource): ResourceResource
    {
        return new ResourceResource($resource);
    }

    public function update(UpdateResourceRequest $request, Resource $resource): ResourceResource
    {
        $resource->update($request->validatedPayload());

        return new ResourceResource($resource->fresh());
    }

    public function destroy(Resource $resource): JsonResponse
    {
        $resource->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(ReorderSortRequest $request): JsonResponse
    {
        foreach ($request->validated('items') as $item) {
            Resource::query()
                ->whereKey($item['id'])
                ->update(['sort_order' => $item['sortOrder']]);
        }

        return response()->json(['message' => 'Reordered']);
    }
}

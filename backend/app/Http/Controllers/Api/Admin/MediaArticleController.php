<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Http\Requests\Api\Admin\StoreMediaArticleRequest;
use App\Http\Requests\Api\Admin\UpdateMediaArticleRequest;
use App\Http\Resources\Api\Admin\MediaArticleResource;
use App\Models\MediaArticle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MediaArticleController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = MediaArticle::query()
            ->orderByDesc('published_date')
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }

        if ($request->has('isPublished')) {
            $query->where('is_published', filter_var($request->query('isPublished'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($search = $request->query('search')) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('title_ar', 'like', "%{$search}%")
                    ->orWhere('title_en', 'like', "%{$search}%")
                    ->orWhere('key', 'like', "%{$search}%");
            });
        }

        $paginator = $query->paginate($limit);

        return MediaArticleResource::collection($paginator)->additional([
            'meta' => [
                'page' => $paginator->currentPage(),
                'limit' => $paginator->perPage(),
                'total' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(StoreMediaArticleRequest $request): JsonResponse
    {
        $article = MediaArticle::query()->create($request->validatedPayload());

        return (new MediaArticleResource($article))
            ->response()
            ->setStatusCode(201);
    }

    public function show(MediaArticle $mediaArticle): MediaArticleResource
    {
        return new MediaArticleResource($mediaArticle);
    }

    public function update(UpdateMediaArticleRequest $request, MediaArticle $mediaArticle): MediaArticleResource
    {
        $mediaArticle->update($request->validatedPayload());

        return new MediaArticleResource($mediaArticle->fresh());
    }

    public function destroy(MediaArticle $mediaArticle): JsonResponse
    {
        $mediaArticle->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(ReorderSortRequest $request): JsonResponse
    {
        foreach ($request->validated('items') as $item) {
            MediaArticle::query()
                ->whereKey($item['id'])
                ->update(['sort_order' => $item['sortOrder']]);
        }

        return response()->json(['message' => 'Reordered']);
    }
}

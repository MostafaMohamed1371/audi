<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\MediaArticleDetailResource;
use App\Http\Resources\Api\V1\MediaArticleListResource;
use App\Models\MediaArticle;
use App\Support\MediaCategoryResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use InvalidArgumentException;

class MediaController extends Controller
{
    public function index(Request $request, string $category): AnonymousResourceCollection|JsonResponse
    {
        try {
            $mediaCategory = MediaCategoryResolver::resolve($category);
        } catch (InvalidArgumentException) {
            return response()->json(['message' => 'Category not found.'], 404);
        }

        $limit = min(max((int) $request->query('limit', 20), 1), 100);
        $query = MediaArticle::query()
            ->where('category', $mediaCategory->value)
            ->where('is_published', true)
            ->orderByDesc('published_date')
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($year = $request->query('year')) {
            $query->whereYear('published_date', (int) $year);
        }

        if ($month = $request->query('month')) {
            $query->whereMonth('published_date', (int) $month);
        }

        if ($search = $request->query('search')) {
            $isAr = app()->getLocale() === 'ar';
            $titleColumn = $isAr ? 'title_ar' : 'title_en';
            $descriptionColumn = $isAr ? 'description_ar' : 'description_en';

            $query->where(function ($builder) use ($search, $titleColumn, $descriptionColumn) {
                $builder
                    ->where($titleColumn, 'like', "%{$search}%")
                    ->orWhere($descriptionColumn, 'like', "%{$search}%");
            });
        }

        $paginator = $query->paginate($limit);

        return MediaArticleListResource::collection($paginator)->additional([
            'meta' => [
                'page' => $paginator->currentPage(),
                'limit' => $paginator->perPage(),
                'total' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, string $category, string $slug): MediaArticleDetailResource|JsonResponse
    {
        try {
            $mediaCategory = MediaCategoryResolver::resolve($category);
        } catch (InvalidArgumentException) {
            return response()->json(['message' => 'Category not found.'], 404);
        }

        $locale = $request->attributes->get('locale', app()->getLocale());
        $slugColumn = $locale === 'en' ? 'slug_en' : 'slug_ar';

        $article = MediaArticle::query()
            ->where('category', $mediaCategory->value)
            ->where($slugColumn, urldecode($slug))
            ->where('is_published', true)
            ->first();

        if (! $article) {
            return response()->json(['message' => 'Article not found.'], 404);
        }

        return new MediaArticleDetailResource($article);
    }
}

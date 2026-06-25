<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ResourceItemResource;
use App\Models\FocusArea;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ResourcesController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = Resource::query()
            ->with('focusArea')
            ->where('is_published', true)
            ->orderByDesc('published_date')
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($type = $request->query('type', $request->query('resourceType'))) {
            $query->where('resource_type', $type);
        }

        if ($year = $request->query('year')) {
            $query->where('year', (int) $year);
        }

        if ($focusArea = $request->query('focusArea', $request->query('focus_area'))) {
            $query->whereHas('focusArea', fn ($builder) => $builder->where('slug', $focusArea));
        }

        if ($search = $request->query('search')) {
            $isAr = app()->getLocale() === 'ar';
            $titleColumn = $isAr ? 'title_ar' : 'title_en';

            $query->where($titleColumn, 'like', "%{$search}%");
        }

        $paginator = $query->paginate($limit);

        return ResourceItemResource::collection($paginator)->additional([
            'meta' => [
                'page' => $paginator->currentPage(),
                'limit' => $paginator->perPage(),
                'total' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
            ],
            'filters' => [
                'types' => Resource::query()
                    ->where('is_published', true)
                    ->whereNotNull('resource_type')
                    ->distinct()
                    ->orderBy('resource_type')
                    ->pluck('resource_type')
                    ->values()
                    ->all(),
                'years' => Resource::query()
                    ->where('is_published', true)
                    ->whereNotNull('year')
                    ->distinct()
                    ->orderByDesc('year')
                    ->pluck('year')
                    ->values()
                    ->all(),
                'focusAreas' => FocusArea::query()
                    ->where('is_published', true)
                    ->orderBy('sort_order')
                    ->get(['slug', 'title_ar', 'title_en'])
                    ->map(fn (FocusArea $area) => [
                        'slug' => $area->slug,
                        'title' => app()->getLocale() === 'ar' ? $area->title_ar : $area->title_en,
                    ])
                    ->values()
                    ->all(),
            ],
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Models\DirectoryProject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DirectoryProjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $paginator = DirectoryProject::query()->ordered()->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (DirectoryProject $project) => $this->transform($project))->values(),
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
            'cityAr' => ['required', 'string', 'max:255'],
            'cityEn' => ['required', 'string', 'max:255'],
            'countryAr' => ['required', 'string', 'max:255'],
            'countryEn' => ['required', 'string', 'max:255'],
            'startDate' => ['nullable', 'string', 'max:50'],
            'endDate' => ['nullable', 'string', 'max:50'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $project = DirectoryProject::query()->create([
            'number' => $validated['number'],
            'city_ar' => $validated['cityAr'],
            'city_en' => $validated['cityEn'],
            'country_ar' => $validated['countryAr'],
            'country_en' => $validated['countryEn'],
            'start_date' => $validated['startDate'] ?? null,
            'end_date' => $validated['endDate'] ?? null,
            'sort_order' => $validated['sortOrder'] ?? 0,
        ]);

        return response()->json(['data' => $this->transform($project)], 201);
    }

    public function show(DirectoryProject $directoryProject): JsonResponse
    {
        return response()->json(['data' => $this->transform($directoryProject)]);
    }

    public function update(Request $request, DirectoryProject $directoryProject): JsonResponse
    {
        $validated = $request->validate([
            'number' => ['sometimes', 'string', 'max:10'],
            'cityAr' => ['sometimes', 'string', 'max:255'],
            'cityEn' => ['sometimes', 'string', 'max:255'],
            'countryAr' => ['sometimes', 'string', 'max:255'],
            'countryEn' => ['sometimes', 'string', 'max:255'],
            'startDate' => ['sometimes', 'nullable', 'string', 'max:50'],
            'endDate' => ['sometimes', 'nullable', 'string', 'max:50'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $payload = [];

        if (array_key_exists('number', $validated)) {
            $payload['number'] = $validated['number'];
        }
        if (array_key_exists('cityAr', $validated)) {
            $payload['city_ar'] = $validated['cityAr'];
        }
        if (array_key_exists('cityEn', $validated)) {
            $payload['city_en'] = $validated['cityEn'];
        }
        if (array_key_exists('countryAr', $validated)) {
            $payload['country_ar'] = $validated['countryAr'];
        }
        if (array_key_exists('countryEn', $validated)) {
            $payload['country_en'] = $validated['countryEn'];
        }
        if (array_key_exists('startDate', $validated)) {
            $payload['start_date'] = $validated['startDate'];
        }
        if (array_key_exists('endDate', $validated)) {
            $payload['end_date'] = $validated['endDate'];
        }
        if (array_key_exists('sortOrder', $validated)) {
            $payload['sort_order'] = $validated['sortOrder'];
        }

        $directoryProject->update($payload);

        return response()->json(['data' => $this->transform($directoryProject->fresh())]);
    }

    public function destroy(DirectoryProject $directoryProject): JsonResponse
    {
        $directoryProject->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(ReorderSortRequest $request): JsonResponse
    {
        foreach ($request->validated('items') as $item) {
            DirectoryProject::query()
                ->whereKey($item['id'])
                ->update(['sort_order' => $item['sortOrder']]);
        }

        return response()->json(['message' => 'Reordered']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(DirectoryProject $project): array
    {
        return [
            'id' => $project->id,
            'number' => $project->number,
            'cityAr' => $project->city_ar,
            'cityEn' => $project->city_en,
            'countryAr' => $project->country_ar,
            'countryEn' => $project->country_en,
            'startDate' => $project->start_date,
            'endDate' => $project->end_date,
            'sortOrder' => $project->sort_order,
            'createdAt' => $project->created_at?->toIso8601String(),
            'updatedAt' => $project->updated_at?->toIso8601String(),
        ];
    }
}

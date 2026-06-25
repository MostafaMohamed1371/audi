<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Models\TrainingCourse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrainingCourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $paginator = TrainingCourse::query()->ordered()->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (TrainingCourse $course) => $this->transform($course))->values(),
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
            'titleAr' => ['required', 'string', 'max:255'],
            'titleEn' => ['required', 'string', 'max:255'],
            'countAr' => ['required', 'string', 'max:255'],
            'countEn' => ['required', 'string', 'max:255'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $course = TrainingCourse::query()->create([
            'title_ar' => $validated['titleAr'],
            'title_en' => $validated['titleEn'],
            'count_ar' => $validated['countAr'],
            'count_en' => $validated['countEn'],
            'sort_order' => $validated['sortOrder'] ?? 0,
        ]);

        return response()->json(['data' => $this->transform($course)], 201);
    }

    public function show(TrainingCourse $trainingCourse): JsonResponse
    {
        return response()->json(['data' => $this->transform($trainingCourse)]);
    }

    public function update(Request $request, TrainingCourse $trainingCourse): JsonResponse
    {
        $validated = $request->validate([
            'titleAr' => ['sometimes', 'string', 'max:255'],
            'titleEn' => ['sometimes', 'string', 'max:255'],
            'countAr' => ['sometimes', 'string', 'max:255'],
            'countEn' => ['sometimes', 'string', 'max:255'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $payload = [];

        if (array_key_exists('titleAr', $validated)) {
            $payload['title_ar'] = $validated['titleAr'];
        }
        if (array_key_exists('titleEn', $validated)) {
            $payload['title_en'] = $validated['titleEn'];
        }
        if (array_key_exists('countAr', $validated)) {
            $payload['count_ar'] = $validated['countAr'];
        }
        if (array_key_exists('countEn', $validated)) {
            $payload['count_en'] = $validated['countEn'];
        }
        if (array_key_exists('sortOrder', $validated)) {
            $payload['sort_order'] = $validated['sortOrder'];
        }

        $trainingCourse->update($payload);

        return response()->json(['data' => $this->transform($trainingCourse->fresh())]);
    }

    public function destroy(TrainingCourse $trainingCourse): JsonResponse
    {
        $trainingCourse->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(ReorderSortRequest $request): JsonResponse
    {
        foreach ($request->validated('items') as $item) {
            TrainingCourse::query()
                ->whereKey($item['id'])
                ->update(['sort_order' => $item['sortOrder']]);
        }

        return response()->json(['message' => 'Reordered']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(TrainingCourse $course): array
    {
        return [
            'id' => $course->id,
            'titleAr' => $course->title_ar,
            'titleEn' => $course->title_en,
            'countAr' => $course->count_ar,
            'countEn' => $course->count_en,
            'sortOrder' => $course->sort_order,
            'createdAt' => $course->created_at?->toIso8601String(),
            'updatedAt' => $course->updated_at?->toIso8601String(),
        ];
    }
}

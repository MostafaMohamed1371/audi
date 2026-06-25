<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Models\JobOpening;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JobOpeningController extends Controller
{
    private const TYPES = ['full_time', 'part_time', 'contract', 'internship'];

    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);
        $paginator = JobOpening::query()->ordered()->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (JobOpening $opening) => $this->transform($opening))->values(),
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
        $validated = $request->validate($this->rules(false));

        $opening = JobOpening::query()->create($this->payload($validated));

        return response()->json(['data' => $this->transform($opening)], 201);
    }

    public function show(JobOpening $jobOpening): JsonResponse
    {
        return response()->json(['data' => $this->transform($jobOpening)]);
    }

    public function update(Request $request, JobOpening $jobOpening): JsonResponse
    {
        $validated = $request->validate($this->rules(true));

        $jobOpening->update($this->payload($validated, true));

        return response()->json(['data' => $this->transform($jobOpening->fresh())]);
    }

    public function destroy(JobOpening $jobOpening): JsonResponse
    {
        $jobOpening->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(ReorderSortRequest $request): JsonResponse
    {
        foreach ($request->validated('items') as $item) {
            JobOpening::query()->whereKey($item['id'])->update(['sort_order' => $item['sortOrder']]);
        }

        return response()->json(['message' => 'Reordered']);
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(bool $partial): array
    {
        $required = $partial ? 'sometimes' : 'required';

        return [
            'titleAr' => [$required, 'string', 'max:255'],
            'titleEn' => [$required, 'string', 'max:255'],
            'locationAr' => ['sometimes', 'nullable', 'string', 'max:255'],
            'locationEn' => ['sometimes', 'nullable', 'string', 'max:255'],
            'employmentType' => ['sometimes', Rule::in(self::TYPES)],
            'summaryAr' => ['sometimes', 'nullable', 'string'],
            'summaryEn' => ['sometimes', 'nullable', 'string'],
            'descriptionAr' => ['sometimes', 'nullable', 'array'],
            'descriptionAr.*' => ['string'],
            'descriptionEn' => ['sometimes', 'nullable', 'array'],
            'descriptionEn.*' => ['string'],
            'isPublished' => ['sometimes', 'boolean'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function payload(array $validated, bool $partial = false): array
    {
        $map = [
            'titleAr' => 'title_ar',
            'titleEn' => 'title_en',
            'locationAr' => 'location_ar',
            'locationEn' => 'location_en',
            'employmentType' => 'employment_type',
            'summaryAr' => 'summary_ar',
            'summaryEn' => 'summary_en',
            'descriptionAr' => 'description_ar',
            'descriptionEn' => 'description_en',
            'isPublished' => 'is_published',
            'sortOrder' => 'sort_order',
        ];

        $payload = [];
        foreach ($map as $input => $column) {
            if (array_key_exists($input, $validated)) {
                $payload[$column] = $validated[$input];
            }
        }

        if (! $partial) {
            $payload['employment_type'] = $validated['employmentType'] ?? 'full_time';
            $payload['is_published'] = $validated['isPublished'] ?? true;
            $payload['sort_order'] = $validated['sortOrder'] ?? 0;
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(JobOpening $opening): array
    {
        return [
            'id' => $opening->id,
            'titleAr' => $opening->title_ar,
            'titleEn' => $opening->title_en,
            'locationAr' => $opening->location_ar,
            'locationEn' => $opening->location_en,
            'employmentType' => $opening->employment_type,
            'summaryAr' => $opening->summary_ar,
            'summaryEn' => $opening->summary_en,
            'descriptionAr' => $opening->description_ar ?? [],
            'descriptionEn' => $opening->description_en ?? [],
            'isPublished' => $opening->is_published,
            'sortOrder' => $opening->sort_order,
            'createdAt' => $opening->created_at?->toIso8601String(),
            'updatedAt' => $opening->updated_at?->toIso8601String(),
        ];
    }
}

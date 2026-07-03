<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Models\ProgramSection;
use App\Support\ImageUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProgramSectionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = ProgramSection::query()->with('details')->ordered();

        if ($programId = $request->query('programId', $request->query('program_id'))) {
            $query->where('program_id', $programId);
        }

        if ($tabKey = $request->query('tabKey', $request->query('tab_key'))) {
            $query->where('tab_key', $tabKey);
        }

        $paginator = $query->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (ProgramSection $section) => $this->transform($section))->values(),
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
        $validated = $request->validate($this->rules());

        $section = ProgramSection::query()->create([
            'program_id' => $validated['programId'],
            'tab_key' => $validated['tabKey'],
            'title_ar' => $validated['titleAr'] ?? null,
            'title_en' => $validated['titleEn'] ?? null,
            'image_url' => isset($validated['imageUrl']) ? ImageUrl::normalizeStoredPath($validated['imageUrl']) : null,
            'sort_order' => $validated['sortOrder'] ?? 0,
        ]);

        return response()->json(['data' => $this->transform($section->load('details'))], 201);
    }

    public function show(ProgramSection $programSection): JsonResponse
    {
        $programSection->load('details');

        return response()->json(['data' => $this->transform($programSection)]);
    }

    public function update(Request $request, ProgramSection $programSection): JsonResponse
    {
        $programId = $request->input('programId', $programSection->program_id);

        $validated = $request->validate($this->rules(partial: true, programId: (int) $programId, sectionId: $programSection->id));

        $payload = [];

        if (array_key_exists('programId', $validated)) {
            $payload['program_id'] = $validated['programId'];
        }
        if (array_key_exists('tabKey', $validated)) {
            $payload['tab_key'] = $validated['tabKey'];
        }
        if (array_key_exists('titleAr', $validated)) {
            $payload['title_ar'] = $validated['titleAr'];
        }
        if (array_key_exists('titleEn', $validated)) {
            $payload['title_en'] = $validated['titleEn'];
        }
        if (array_key_exists('imageUrl', $validated)) {
            $payload['image_url'] = ImageUrl::normalizeStoredPath($validated['imageUrl']);
        }
        if (array_key_exists('sortOrder', $validated)) {
            $payload['sort_order'] = $validated['sortOrder'];
        }

        $programSection->update($payload);

        return response()->json(['data' => $this->transform($programSection->fresh()->load('details'))]);
    }

    public function destroy(ProgramSection $programSection): JsonResponse
    {
        $programSection->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(ReorderSortRequest $request): JsonResponse
    {
        foreach ($request->validated('items') as $item) {
            ProgramSection::query()
                ->whereKey($item['id'])
                ->update(['sort_order' => $item['sortOrder']]);
        }

        return response()->json(['message' => 'Reordered']);
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(bool $partial = false, ?int $programId = null, ?int $sectionId = null): array
    {
        $programId = $programId ?? request()->input('programId');

        return [
            'programId' => array_filter([
                $partial ? 'sometimes' : 'required',
                'integer',
                Rule::exists('programs', 'id'),
            ]),
            'tabKey' => array_filter([
                $partial ? 'sometimes' : 'required',
                'string',
                'max:120',
                $programId !== null
                    ? Rule::unique('program_sections', 'tab_key')
                        ->where(fn ($query) => $query->where('program_id', $programId))
                        ->ignore($sectionId)
                    : null,
            ]),
            'titleAr' => ['sometimes', 'nullable', 'string', 'max:255'],
            'titleEn' => ['sometimes', 'nullable', 'string', 'max:255'],
            'imageUrl' => ['sometimes', 'nullable', 'string', 'max:500'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(ProgramSection $section): array
    {
        $details = $section->relationLoaded('details') ? $section->details : null;

        return [
            'id' => $section->id,
            'programId' => $section->program_id,
            'tabKey' => $section->tab_key,
            'titleAr' => $section->title_ar,
            'titleEn' => $section->title_en,
            'imageUrl' => ImageUrl::api($section->image_url),
            'sortOrder' => $section->sort_order,
            'detailId' => $details?->id,
            'createdAt' => $section->created_at?->toIso8601String(),
            'updatedAt' => $section->updated_at?->toIso8601String(),
        ];
    }
}

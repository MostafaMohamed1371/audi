<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Models\ProgramSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProgramSectionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = ProgramSection::query()->ordered();

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
        $validated = $request->validate([
            'programId' => ['required', 'integer', Rule::exists('programs', 'id')],
            'tabKey' => [
                'required',
                'string',
                'max:120',
                Rule::unique('program_sections', 'tab_key')->where(
                    fn ($query) => $query->where('program_id', $request->input('programId'))
                ),
            ],
            'titleAr' => ['required', 'string', 'max:255'],
            'titleEn' => ['required', 'string', 'max:255'],
            'introAr' => ['nullable', 'string'],
            'introEn' => ['nullable', 'string'],
            'bodyAr' => ['nullable', 'array'],
            'bodyEn' => ['nullable', 'array'],
            'imageUrl' => ['nullable', 'string', 'max:500'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $section = ProgramSection::query()->create([
            'program_id' => $validated['programId'],
            'tab_key' => $validated['tabKey'],
            'title_ar' => $validated['titleAr'],
            'title_en' => $validated['titleEn'],
            'intro_ar' => $validated['introAr'] ?? null,
            'intro_en' => $validated['introEn'] ?? null,
            'body_ar' => $validated['bodyAr'] ?? null,
            'body_en' => $validated['bodyEn'] ?? null,
            'image_url' => $validated['imageUrl'] ?? null,
            'sort_order' => $validated['sortOrder'] ?? 0,
        ]);

        return response()->json(['data' => $this->transform($section)], 201);
    }

    public function show(ProgramSection $programSection): JsonResponse
    {
        return response()->json(['data' => $this->transform($programSection)]);
    }

    public function update(Request $request, ProgramSection $programSection): JsonResponse
    {
        $programId = $request->input('programId', $programSection->program_id);

        $validated = $request->validate([
            'programId' => ['sometimes', 'integer', Rule::exists('programs', 'id')],
            'tabKey' => [
                'sometimes',
                'string',
                'max:120',
                Rule::unique('program_sections', 'tab_key')
                    ->where(fn ($query) => $query->where('program_id', $programId))
                    ->ignore($programSection->id),
            ],
            'titleAr' => ['sometimes', 'string', 'max:255'],
            'titleEn' => ['sometimes', 'string', 'max:255'],
            'introAr' => ['sometimes', 'nullable', 'string'],
            'introEn' => ['sometimes', 'nullable', 'string'],
            'bodyAr' => ['sometimes', 'nullable', 'array'],
            'bodyEn' => ['sometimes', 'nullable', 'array'],
            'imageUrl' => ['sometimes', 'nullable', 'string', 'max:500'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

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
        if (array_key_exists('introAr', $validated)) {
            $payload['intro_ar'] = $validated['introAr'];
        }
        if (array_key_exists('introEn', $validated)) {
            $payload['intro_en'] = $validated['introEn'];
        }
        if (array_key_exists('bodyAr', $validated)) {
            $payload['body_ar'] = $validated['bodyAr'];
        }
        if (array_key_exists('bodyEn', $validated)) {
            $payload['body_en'] = $validated['bodyEn'];
        }
        if (array_key_exists('imageUrl', $validated)) {
            $payload['image_url'] = $validated['imageUrl'];
        }
        if (array_key_exists('sortOrder', $validated)) {
            $payload['sort_order'] = $validated['sortOrder'];
        }

        $programSection->update($payload);

        return response()->json(['data' => $this->transform($programSection->fresh())]);
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
    private function transform(ProgramSection $section): array
    {
        return [
            'id' => $section->id,
            'programId' => $section->program_id,
            'tabKey' => $section->tab_key,
            'titleAr' => $section->title_ar,
            'titleEn' => $section->title_en,
            'introAr' => $section->intro_ar,
            'introEn' => $section->intro_en,
            'bodyAr' => $section->body_ar,
            'bodyEn' => $section->body_en,
            'imageUrl' => $section->image_url,
            'sortOrder' => $section->sort_order,
            'createdAt' => $section->created_at?->toIso8601String(),
            'updatedAt' => $section->updated_at?->toIso8601String(),
        ];
    }
}

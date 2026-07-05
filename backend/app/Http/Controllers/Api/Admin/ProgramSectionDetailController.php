<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramSection;
use App\Models\ProgramSectionDetail;
use App\Services\Programs\ProgramSectionNestedContentSync;
use App\Support\ImageUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProgramSectionDetailController extends Controller
{
    public function __construct(
        private readonly ProgramSectionNestedContentSync $nestedContentSync,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = ProgramSectionDetail::query()->with('section');

        if ($programSectionId = $request->query('programSectionId', $request->query('program_section_id'))) {
            $query->where('program_section_id', $programSectionId);
        }

        if ($programId = $request->query('programId', $request->query('program_id'))) {
            $query->whereHas('section', fn ($q) => $q->where('program_id', $programId));
        }

        $paginator = $query->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (ProgramSectionDetail $detail) => $this->transform($detail))->values(),
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

        $detail = ProgramSectionDetail::query()->create([
            'program_section_id' => $validated['programSectionId'],
            'title_ar' => $validated['titleAr'] ?? null,
            'title_en' => $validated['titleEn'] ?? null,
            'image_url' => isset($validated['imageUrl']) ? ImageUrl::normalizeStoredPath($validated['imageUrl']) : null,
            'intro_ar' => $validated['introAr'] ?? null,
            'intro_en' => $validated['introEn'] ?? null,
            'body_ar' => $validated['bodyAr'] ?? null,
            'body_en' => $validated['bodyEn'] ?? null,
        ]);

        $this->syncNestedContent($validated);

        return response()->json(['data' => $this->transform($detail->load('section'))], 201);
    }

    public function show(ProgramSectionDetail $programSectionDetail): JsonResponse
    {
        return response()->json(['data' => $this->transform($programSectionDetail->load('section'))]);
    }

    public function update(Request $request, ProgramSectionDetail $programSectionDetail): JsonResponse
    {
        $validated = $request->validate($this->rules(partial: true, detailId: $programSectionDetail->id));

        $payload = [];

        if (array_key_exists('programSectionId', $validated)) {
            $payload['program_section_id'] = $validated['programSectionId'];
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

        $programSectionDetail->update($payload);

        $this->syncNestedContent(array_merge(
            ['programSectionId' => $programSectionDetail->program_section_id],
            $validated,
        ));

        return response()->json(['data' => $this->transform($programSectionDetail->fresh()->load('section'))]);
    }

    public function destroy(ProgramSectionDetail $programSectionDetail): JsonResponse
    {
        $programSectionDetail->delete();

        return response()->json(['message' => 'Deleted']);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function syncNestedContent(array $validated): void
    {
        $sectionId = $validated['programSectionId'] ?? null;
        if (! $sectionId) {
            return;
        }

        $section = ProgramSection::query()->find($sectionId);
        if (! $section) {
            return;
        }

        $this->nestedContentSync->syncFromDetail(
            $section,
            $validated['bodyAr'] ?? null,
            $validated['bodyEn'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(bool $partial = false, ?int $detailId = null): array
    {
        return [
            'programSectionId' => array_filter([
                $partial ? 'sometimes' : 'required',
                'integer',
                Rule::exists('program_sections', 'id'),
                $detailId === null
                    ? Rule::unique('program_section_details', 'program_section_id')
                    : Rule::unique('program_section_details', 'program_section_id')->ignore($detailId),
            ]),
            'titleAr' => ['sometimes', 'nullable', 'string', 'max:255'],
            'titleEn' => ['sometimes', 'nullable', 'string', 'max:255'],
            'imageUrl' => ['sometimes', 'nullable', 'string', 'max:500'],
            'introAr' => ['sometimes', 'nullable', 'string'],
            'introEn' => ['sometimes', 'nullable', 'string'],
            'bodyAr' => ['sometimes', 'nullable', 'array'],
            'bodyEn' => ['sometimes', 'nullable', 'array'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(ProgramSectionDetail $detail): array
    {
        $section = $detail->section;

        return [
            'id' => $detail->id,
            'programSectionId' => $detail->program_section_id,
            'programId' => $section?->program_id,
            'tabKey' => $section?->tab_key,
            'titleAr' => $detail->title_ar,
            'titleEn' => $detail->title_en,
            'imageUrl' => ImageUrl::api($detail->image_url),
            'introAr' => $detail->intro_ar,
            'introEn' => $detail->intro_en,
            'bodyAr' => $detail->body_ar,
            'bodyEn' => $detail->body_en,
            'sectionTitleAr' => $section?->title_ar,
            'sectionTitleEn' => $section?->title_en,
            'sectionImageUrl' => ImageUrl::api($section?->image_url),
            'createdAt' => $detail->created_at?->toIso8601String(),
            'updatedAt' => $detail->updated_at?->toIso8601String(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutContent;
use App\Support\AboutContentBodyRules;
use App\Support\ImageUrl;
use App\Support\ProgramContentKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AboutContentController extends Controller
{
    public function index(): JsonResponse
    {
        $data = AboutContent::query()
            ->orderBy('section_key')
            ->get()
            ->map(fn (AboutContent $content) => $this->transform($content))
            ->values();

        return response()->json(['data' => $data]);
    }

    public function store(Request $request): JsonResponse
    {
        if ($request->filled('programSectionId')) {
            return $this->storeSectionPresentation($request);
        }

        $sectionKey = (string) $request->input('sectionKey', '');
        $parsed = ProgramContentKey::parse($sectionKey);
        $isLegacyProgramSection = $parsed !== null && $parsed['type'] === 'section';

        $validated = $request->validate(array_merge([
            'sectionKey' => ['required', 'string', 'max:120', Rule::unique('about_content', 'section_key')],
            'titleAr' => [$isLegacyProgramSection ? 'required' : 'nullable', 'string', 'max:255'],
            'titleEn' => [$isLegacyProgramSection ? 'required' : 'nullable', 'string', 'max:255'],
            'bodyAr' => ['nullable', 'array'],
            'bodyEn' => ['nullable', 'array'],
            'imageUrl' => ['nullable', 'string', 'max:500'],
        ], AboutContentBodyRules::rules($sectionKey)));

        $content = AboutContent::query()->create([
            'section_key' => $validated['sectionKey'],
            'title_ar' => $validated['titleAr'] ?? null,
            'title_en' => $validated['titleEn'] ?? null,
            'body_ar' => $validated['bodyAr'] ?? null,
            'body_en' => $validated['bodyEn'] ?? null,
            'image_url' => $validated['imageUrl'] ?? null,
        ]);

        return response()->json(['data' => $this->transform($content)], 201);
    }

    public function show(AboutContent $aboutContent): JsonResponse
    {
        return response()->json(['data' => $this->transform($aboutContent)]);
    }

    public function update(Request $request, AboutContent $aboutContent): JsonResponse
    {
        if ($request->filled('programSectionId') || $aboutContent->program_section_id) {
            return $this->updateSectionPresentation($request, $aboutContent);
        }

        $sectionKey = $request->input('sectionKey', $aboutContent->section_key);
        $parsed = ProgramContentKey::parse((string) $sectionKey);
        $isLegacyProgramSection = $parsed !== null && $parsed['type'] === 'section';

        $validated = $request->validate(array_merge([
            'sectionKey' => [
                'sometimes',
                'string',
                'max:120',
                Rule::unique('about_content', 'section_key')->ignore($aboutContent->id),
            ],
            'titleAr' => ['sometimes', $isLegacyProgramSection ? 'required' : 'nullable', 'string', 'max:255'],
            'titleEn' => ['sometimes', $isLegacyProgramSection ? 'required' : 'nullable', 'string', 'max:255'],
            'bodyAr' => ['sometimes', 'nullable', 'array'],
            'bodyEn' => ['sometimes', 'nullable', 'array'],
            'imageUrl' => ['sometimes', 'nullable', 'string', 'max:500'],
        ], AboutContentBodyRules::rules((string) $sectionKey, partial: true)));

        $payload = [];

        if (array_key_exists('sectionKey', $validated)) {
            $payload['section_key'] = $validated['sectionKey'];
        }
        if (array_key_exists('titleAr', $validated)) {
            $payload['title_ar'] = $validated['titleAr'];
        }
        if (array_key_exists('titleEn', $validated)) {
            $payload['title_en'] = $validated['titleEn'];
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

        $aboutContent->update($payload);

        return response()->json(['data' => $this->transform($aboutContent->fresh())]);
    }

    public function destroy(AboutContent $aboutContent): JsonResponse
    {
        $aboutContent->delete();

        return response()->json(['message' => 'Deleted']);
    }

    private function storeSectionPresentation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'programSectionId' => [
                'required',
                'integer',
                Rule::exists('program_sections', 'id'),
                Rule::unique('about_content', 'program_section_id'),
            ],
            'titleAr' => ['required', 'string', 'max:255'],
            'titleEn' => ['required', 'string', 'max:255'],
            'imageUrl' => ['nullable', 'string', 'max:500'],
            'sectionKey' => ['sometimes', 'string', 'max:120', Rule::unique('about_content', 'section_key')],
        ]);

        $sectionKey = $validated['sectionKey'] ?? 'program_section_'.$validated['programSectionId'];

        $content = AboutContent::query()->create([
            'section_key' => $sectionKey,
            'program_section_id' => $validated['programSectionId'],
            'title_ar' => $validated['titleAr'],
            'title_en' => $validated['titleEn'],
            'image_url' => $validated['imageUrl'] ?? null,
        ]);

        return response()->json(['data' => $this->transform($content)], 201);
    }

    private function updateSectionPresentation(Request $request, AboutContent $aboutContent): JsonResponse
    {
        $programSectionId = $request->input('programSectionId', $aboutContent->program_section_id);

        $validated = $request->validate([
            'programSectionId' => [
                'sometimes',
                'integer',
                Rule::exists('program_sections', 'id'),
                Rule::unique('about_content', 'program_section_id')->ignore($aboutContent->id),
            ],
            'titleAr' => ['sometimes', 'required', 'string', 'max:255'],
            'titleEn' => ['sometimes', 'required', 'string', 'max:255'],
            'imageUrl' => ['sometimes', 'nullable', 'string', 'max:500'],
        ]);

        $payload = [];

        if (array_key_exists('programSectionId', $validated)) {
            $payload['program_section_id'] = $validated['programSectionId'];
            $payload['section_key'] = 'program_section_'.$validated['programSectionId'];
        } elseif ($programSectionId) {
            $payload['section_key'] = 'program_section_'.$programSectionId;
        }

        if (array_key_exists('titleAr', $validated)) {
            $payload['title_ar'] = $validated['titleAr'];
        }
        if (array_key_exists('titleEn', $validated)) {
            $payload['title_en'] = $validated['titleEn'];
        }
        if (array_key_exists('imageUrl', $validated)) {
            $payload['image_url'] = $validated['imageUrl'];
        }

        $aboutContent->update($payload);

        return response()->json(['data' => $this->transform($aboutContent->fresh())]);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(AboutContent $content): array
    {
        return [
            'id' => $content->id,
            'sectionKey' => $content->section_key,
            'programSectionId' => $content->program_section_id,
            'titleAr' => $content->title_ar,
            'titleEn' => $content->title_en,
            'bodyAr' => ImageUrl::mapBodyPaths($content->body_ar),
            'bodyEn' => ImageUrl::mapBodyPaths($content->body_en),
            'imageUrl' => ImageUrl::api($content->image_url),
            'createdAt' => $content->created_at?->toIso8601String(),
            'updatedAt' => $content->updated_at?->toIso8601String(),
        ];
    }
}

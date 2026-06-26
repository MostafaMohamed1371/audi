<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutContent;
use App\Support\AboutContentBodyRules;
use App\Support\ImageUrl;
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
        $validated = $request->validate(array_merge([
            'sectionKey' => ['required', 'string', 'max:120', Rule::unique('about_content', 'section_key')],
            'titleAr' => ['nullable', 'string', 'max:255'],
            'titleEn' => ['nullable', 'string', 'max:255'],
            'bodyAr' => ['nullable', 'array'],
            'bodyEn' => ['nullable', 'array'],
            'imageUrl' => ['nullable', 'string', 'max:500'],
        ], AboutContentBodyRules::rules($request->input('sectionKey', ''))));

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
        $sectionKey = $request->input('sectionKey', $aboutContent->section_key);

        $validated = $request->validate(array_merge([
            'sectionKey' => [
                'sometimes',
                'string',
                'max:120',
                Rule::unique('about_content', 'section_key')->ignore($aboutContent->id),
            ],
            'titleAr' => ['sometimes', 'nullable', 'string', 'max:255'],
            'titleEn' => ['sometimes', 'nullable', 'string', 'max:255'],
            'bodyAr' => ['sometimes', 'nullable', 'array'],
            'bodyEn' => ['sometimes', 'nullable', 'array'],
            'imageUrl' => ['sometimes', 'nullable', 'string', 'max:500'],
        ], AboutContentBodyRules::rules($sectionKey, partial: true)));

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

    /**
     * @return array<string, mixed>
     */
    private function transform(AboutContent $content): array
    {
        return [
            'id' => $content->id,
            'sectionKey' => $content->section_key,
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

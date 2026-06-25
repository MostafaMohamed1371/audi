<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProgramController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = Program::query()->orderBy('sort_order')->orderBy('id');

        if ($slug = $request->query('slug')) {
            $query->where('slug', $slug);
        }

        $paginator = $query->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (Program $program) => $this->transform($program))->values(),
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
            'slug' => ['required', 'string', 'max:120', Rule::unique('programs', 'slug')],
            'titleAr' => ['required', 'string', 'max:255'],
            'titleEn' => ['required', 'string', 'max:255'],
            'heroIntroAr' => ['nullable', 'string'],
            'heroIntroEn' => ['nullable', 'string'],
            'cardDescriptionAr' => ['nullable', 'string'],
            'cardDescriptionEn' => ['nullable', 'string'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $program = Program::query()->create([
            'slug' => $validated['slug'],
            'title_ar' => $validated['titleAr'],
            'title_en' => $validated['titleEn'],
            'hero_intro_ar' => $validated['heroIntroAr'] ?? null,
            'hero_intro_en' => $validated['heroIntroEn'] ?? null,
            'card_description_ar' => $validated['cardDescriptionAr'] ?? null,
            'card_description_en' => $validated['cardDescriptionEn'] ?? null,
            'sort_order' => $validated['sortOrder'] ?? 0,
        ]);

        return response()->json(['data' => $this->transform($program)], 201);
    }

    public function show(Program $program): JsonResponse
    {
        return response()->json(['data' => $this->transform($program)]);
    }

    public function update(Request $request, Program $program): JsonResponse
    {
        $validated = $request->validate([
            'titleAr' => ['sometimes', 'string', 'max:255'],
            'titleEn' => ['sometimes', 'string', 'max:255'],
            'heroIntroAr' => ['sometimes', 'nullable', 'string'],
            'heroIntroEn' => ['sometimes', 'nullable', 'string'],
            'cardDescriptionAr' => ['sometimes', 'nullable', 'string'],
            'cardDescriptionEn' => ['sometimes', 'nullable', 'string'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $payload = [];

        if (array_key_exists('titleAr', $validated)) {
            $payload['title_ar'] = $validated['titleAr'];
        }
        if (array_key_exists('titleEn', $validated)) {
            $payload['title_en'] = $validated['titleEn'];
        }
        if (array_key_exists('heroIntroAr', $validated)) {
            $payload['hero_intro_ar'] = $validated['heroIntroAr'];
        }
        if (array_key_exists('heroIntroEn', $validated)) {
            $payload['hero_intro_en'] = $validated['heroIntroEn'];
        }
        if (array_key_exists('cardDescriptionAr', $validated)) {
            $payload['card_description_ar'] = $validated['cardDescriptionAr'];
        }
        if (array_key_exists('cardDescriptionEn', $validated)) {
            $payload['card_description_en'] = $validated['cardDescriptionEn'];
        }
        if (array_key_exists('sortOrder', $validated)) {
            $payload['sort_order'] = $validated['sortOrder'];
        }

        $program->update($payload);

        return response()->json(['data' => $this->transform($program->fresh())]);
    }

    public function destroy(Program $program): JsonResponse
    {
        $program->delete();

        return response()->json(['message' => 'Deleted']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(Program $program): array
    {
        return [
            'id' => $program->id,
            'slug' => $program->slug,
            'titleAr' => $program->title_ar,
            'titleEn' => $program->title_en,
            'heroIntroAr' => $program->hero_intro_ar,
            'heroIntroEn' => $program->hero_intro_en,
            'cardDescriptionAr' => $program->card_description_ar,
            'cardDescriptionEn' => $program->card_description_en,
            'sortOrder' => $program->sort_order,
            'createdAt' => $program->created_at?->toIso8601String(),
            'updatedAt' => $program->updated_at?->toIso8601String(),
        ];
    }
}

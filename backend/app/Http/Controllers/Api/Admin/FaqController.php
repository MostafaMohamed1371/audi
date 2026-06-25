<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);
        $paginator = Faq::query()->ordered()->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (Faq $faq) => $this->transform($faq))->values(),
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
            'category' => ['nullable', 'string', 'max:120'],
            'questionAr' => ['required', 'string', 'max:500'],
            'questionEn' => ['required', 'string', 'max:500'],
            'answerAr' => ['required', 'string'],
            'answerEn' => ['required', 'string'],
            'isPublished' => ['sometimes', 'boolean'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $faq = Faq::query()->create([
            'category' => $validated['category'] ?? null,
            'question_ar' => $validated['questionAr'],
            'question_en' => $validated['questionEn'],
            'answer_ar' => $validated['answerAr'],
            'answer_en' => $validated['answerEn'],
            'is_published' => $validated['isPublished'] ?? true,
            'sort_order' => $validated['sortOrder'] ?? 0,
        ]);

        return response()->json(['data' => $this->transform($faq)], 201);
    }

    public function show(Faq $faq): JsonResponse
    {
        return response()->json(['data' => $this->transform($faq)]);
    }

    public function update(Request $request, Faq $faq): JsonResponse
    {
        $validated = $request->validate([
            'category' => ['sometimes', 'nullable', 'string', 'max:120'],
            'questionAr' => ['sometimes', 'string', 'max:500'],
            'questionEn' => ['sometimes', 'string', 'max:500'],
            'answerAr' => ['sometimes', 'string'],
            'answerEn' => ['sometimes', 'string'],
            'isPublished' => ['sometimes', 'boolean'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $map = [
            'category' => 'category',
            'questionAr' => 'question_ar',
            'questionEn' => 'question_en',
            'answerAr' => 'answer_ar',
            'answerEn' => 'answer_en',
            'isPublished' => 'is_published',
            'sortOrder' => 'sort_order',
        ];

        $payload = [];
        foreach ($map as $input => $column) {
            if (array_key_exists($input, $validated)) {
                $payload[$column] = $validated[$input];
            }
        }

        $faq->update($payload);

        return response()->json(['data' => $this->transform($faq->fresh())]);
    }

    public function destroy(Faq $faq): JsonResponse
    {
        $faq->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(ReorderSortRequest $request): JsonResponse
    {
        foreach ($request->validated('items') as $item) {
            Faq::query()->whereKey($item['id'])->update(['sort_order' => $item['sortOrder']]);
        }

        return response()->json(['message' => 'Reordered']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(Faq $faq): array
    {
        return [
            'id' => $faq->id,
            'category' => $faq->category,
            'questionAr' => $faq->question_ar,
            'questionEn' => $faq->question_en,
            'answerAr' => $faq->answer_ar,
            'answerEn' => $faq->answer_en,
            'isPublished' => $faq->is_published,
            'sortOrder' => $faq->sort_order,
            'createdAt' => $faq->created_at?->toIso8601String(),
            'updatedAt' => $faq->updated_at?->toIso8601String(),
        ];
    }
}

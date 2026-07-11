<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Models\DirectoryDiscussion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DirectoryDiscussionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = DirectoryDiscussion::query()->ordered();

        if ($type = $request->query('directoryType', $request->query('directory_type'))) {
            $query->where('directory_type', $type);
        }

        if ($number = $request->query('directoryNumber', $request->query('directory_number'))) {
            $query->where('directory_number', $number);
        }

        $paginator = $query->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (DirectoryDiscussion $item) => $this->transform($item))->values(),
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
            'directoryType' => ['required', Rule::in(['cities', 'projects', 'organizations', 'publications'])],
            'directoryNumber' => ['required', 'string', 'max:10'],
            'authorNameAr' => ['required', 'string', 'max:255'],
            'authorNameEn' => ['required', 'string', 'max:255'],
            'bodyAr' => ['required', 'string'],
            'bodyEn' => ['required', 'string'],
            'isApproved' => ['sometimes', 'boolean'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $discussion = DirectoryDiscussion::query()->create([
            'directory_type' => $validated['directoryType'],
            'directory_number' => $validated['directoryNumber'],
            'author_name_ar' => $validated['authorNameAr'],
            'author_name_en' => $validated['authorNameEn'],
            'body_ar' => $validated['bodyAr'],
            'body_en' => $validated['bodyEn'],
            'is_approved' => $validated['isApproved'] ?? true,
            'sort_order' => $validated['sortOrder'] ?? 0,
        ]);

        return response()->json(['data' => $this->transform($discussion)], 201);
    }

    public function show(DirectoryDiscussion $directoryDiscussion): JsonResponse
    {
        return response()->json(['data' => $this->transform($directoryDiscussion)]);
    }

    public function update(Request $request, DirectoryDiscussion $directoryDiscussion): JsonResponse
    {
        $validated = $request->validate([
            'directoryType' => ['sometimes', Rule::in(['cities', 'projects', 'organizations', 'publications'])],
            'directoryNumber' => ['sometimes', 'string', 'max:10'],
            'authorNameAr' => ['sometimes', 'string', 'max:255'],
            'authorNameEn' => ['sometimes', 'string', 'max:255'],
            'bodyAr' => ['sometimes', 'string'],
            'bodyEn' => ['sometimes', 'string'],
            'isApproved' => ['sometimes', 'boolean'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $payload = [];

        if (array_key_exists('directoryType', $validated)) {
            $payload['directory_type'] = $validated['directoryType'];
        }
        if (array_key_exists('directoryNumber', $validated)) {
            $payload['directory_number'] = $validated['directoryNumber'];
        }
        if (array_key_exists('authorNameAr', $validated)) {
            $payload['author_name_ar'] = $validated['authorNameAr'];
        }
        if (array_key_exists('authorNameEn', $validated)) {
            $payload['author_name_en'] = $validated['authorNameEn'];
        }
        if (array_key_exists('bodyAr', $validated)) {
            $payload['body_ar'] = $validated['bodyAr'];
        }
        if (array_key_exists('bodyEn', $validated)) {
            $payload['body_en'] = $validated['bodyEn'];
        }
        if (array_key_exists('isApproved', $validated)) {
            $payload['is_approved'] = $validated['isApproved'];
        }
        if (array_key_exists('sortOrder', $validated)) {
            $payload['sort_order'] = $validated['sortOrder'];
        }

        $directoryDiscussion->update($payload);

        return response()->json(['data' => $this->transform($directoryDiscussion->fresh())]);
    }

    public function destroy(DirectoryDiscussion $directoryDiscussion): JsonResponse
    {
        $directoryDiscussion->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(ReorderSortRequest $request): JsonResponse
    {
        foreach ($request->validated('items') as $item) {
            DirectoryDiscussion::query()
                ->whereKey($item['id'])
                ->update(['sort_order' => $item['sortOrder']]);
        }

        return response()->json(['message' => 'Reordered']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(DirectoryDiscussion $discussion): array
    {
        return [
            'id' => $discussion->id,
            'directoryType' => $discussion->directory_type,
            'directoryNumber' => $discussion->directory_number,
            'authorNameAr' => $discussion->author_name_ar,
            'authorNameEn' => $discussion->author_name_en,
            'bodyAr' => $discussion->body_ar,
            'bodyEn' => $discussion->body_en,
            'isApproved' => $discussion->is_approved,
            'sortOrder' => $discussion->sort_order,
            'createdAt' => $discussion->created_at?->toIso8601String(),
            'updatedAt' => $discussion->updated_at?->toIso8601String(),
        ];
    }
}

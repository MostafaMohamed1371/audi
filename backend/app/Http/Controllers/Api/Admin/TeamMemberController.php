<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReorderSortRequest;
use App\Models\TeamMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeamMemberController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = TeamMember::query()->ordered();

        if ($teamSectionId = $request->query('teamSectionId', $request->query('team_section_id'))) {
            $query->where('team_section_id', $teamSectionId);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name_ar', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('role_ar', 'like', "%{$search}%")
                    ->orWhere('role_en', 'like', "%{$search}%");
            });
        }

        $paginator = $query->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (TeamMember $member) => $this->transform($member))->values(),
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
            'teamSectionId' => ['required', 'integer', Rule::exists('team_sections', 'id')],
            'nameAr' => ['required', 'string', 'max:255'],
            'nameEn' => ['required', 'string', 'max:255'],
            'roleAr' => ['required', 'string', 'max:255'],
            'roleEn' => ['required', 'string', 'max:255'],
            'bioAr' => ['nullable', 'string'],
            'bioEn' => ['nullable', 'string'],
            'imageUrl' => ['nullable', 'string', 'max:500'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $member = TeamMember::query()->create([
            'team_section_id' => $validated['teamSectionId'],
            'name_ar' => $validated['nameAr'],
            'name_en' => $validated['nameEn'],
            'role_ar' => $validated['roleAr'],
            'role_en' => $validated['roleEn'],
            'bio_ar' => $validated['bioAr'] ?? null,
            'bio_en' => $validated['bioEn'] ?? null,
            'image_url' => $validated['imageUrl'] ?? null,
            'sort_order' => $validated['sortOrder'] ?? 0,
        ]);

        return response()->json(['data' => $this->transform($member)], 201);
    }

    public function show(TeamMember $teamMember): JsonResponse
    {
        return response()->json(['data' => $this->transform($teamMember)]);
    }

    public function update(Request $request, TeamMember $teamMember): JsonResponse
    {
        $validated = $request->validate([
            'teamSectionId' => ['sometimes', 'integer', Rule::exists('team_sections', 'id')],
            'nameAr' => ['sometimes', 'string', 'max:255'],
            'nameEn' => ['sometimes', 'string', 'max:255'],
            'roleAr' => ['sometimes', 'string', 'max:255'],
            'roleEn' => ['sometimes', 'string', 'max:255'],
            'bioAr' => ['sometimes', 'nullable', 'string'],
            'bioEn' => ['sometimes', 'nullable', 'string'],
            'imageUrl' => ['sometimes', 'nullable', 'string', 'max:500'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ]);

        $payload = [];

        if (array_key_exists('teamSectionId', $validated)) {
            $payload['team_section_id'] = $validated['teamSectionId'];
        }
        if (array_key_exists('nameAr', $validated)) {
            $payload['name_ar'] = $validated['nameAr'];
        }
        if (array_key_exists('nameEn', $validated)) {
            $payload['name_en'] = $validated['nameEn'];
        }
        if (array_key_exists('roleAr', $validated)) {
            $payload['role_ar'] = $validated['roleAr'];
        }
        if (array_key_exists('roleEn', $validated)) {
            $payload['role_en'] = $validated['roleEn'];
        }
        if (array_key_exists('bioAr', $validated)) {
            $payload['bio_ar'] = $validated['bioAr'];
        }
        if (array_key_exists('bioEn', $validated)) {
            $payload['bio_en'] = $validated['bioEn'];
        }
        if (array_key_exists('imageUrl', $validated)) {
            $payload['image_url'] = $validated['imageUrl'];
        }
        if (array_key_exists('sortOrder', $validated)) {
            $payload['sort_order'] = $validated['sortOrder'];
        }

        $teamMember->update($payload);

        return response()->json(['data' => $this->transform($teamMember->fresh())]);
    }

    public function destroy(TeamMember $teamMember): JsonResponse
    {
        $teamMember->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(ReorderSortRequest $request): JsonResponse
    {
        foreach ($request->validated('items') as $item) {
            TeamMember::query()
                ->whereKey($item['id'])
                ->update(['sort_order' => $item['sortOrder']]);
        }

        return response()->json(['message' => 'Reordered']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(TeamMember $member): array
    {
        return [
            'id' => $member->id,
            'teamSectionId' => $member->team_section_id,
            'nameAr' => $member->name_ar,
            'nameEn' => $member->name_en,
            'roleAr' => $member->role_ar,
            'roleEn' => $member->role_en,
            'bioAr' => $member->bio_ar,
            'bioEn' => $member->bio_en,
            'imageUrl' => $member->image_url,
            'sortOrder' => $member->sort_order,
            'createdAt' => $member->created_at?->toIso8601String(),
            'updatedAt' => $member->updated_at?->toIso8601String(),
        ];
    }
}

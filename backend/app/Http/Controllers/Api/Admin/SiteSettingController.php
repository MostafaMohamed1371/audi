<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SiteSettingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = SiteSetting::query()->orderBy('group')->orderBy('key');

        if ($group = $request->query('group')) {
            $query->where('group', $group);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('key', 'like', "%{$search}%")
                    ->orWhere('value_ar', 'like', "%{$search}%")
                    ->orWhere('value_en', 'like', "%{$search}%");
            });
        }

        $paginator = $query->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (SiteSetting $setting) => $this->transform($setting))->values(),
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
            'key' => ['required', 'string', 'max:120', Rule::unique('site_settings', 'key')],
            'valueAr' => ['nullable', 'string'],
            'valueEn' => ['nullable', 'string'],
            'group' => ['sometimes', 'string', 'max:60'],
        ]);

        $setting = SiteSetting::query()->create([
            'key' => $validated['key'],
            'value_ar' => $validated['valueAr'] ?? null,
            'value_en' => $validated['valueEn'] ?? null,
            'group' => $validated['group'] ?? 'general',
        ]);

        return response()->json(['data' => $this->transform($setting)], 201);
    }

    public function show(SiteSetting $siteSetting): JsonResponse
    {
        return response()->json(['data' => $this->transform($siteSetting)]);
    }

    public function update(Request $request, SiteSetting $siteSetting): JsonResponse
    {
        $validated = $request->validate([
            'key' => ['sometimes', 'string', 'max:120', Rule::unique('site_settings', 'key')->ignore($siteSetting->id)],
            'valueAr' => ['sometimes', 'nullable', 'string'],
            'valueEn' => ['sometimes', 'nullable', 'string'],
            'group' => ['sometimes', 'string', 'max:60'],
        ]);

        $map = [
            'key' => 'key',
            'valueAr' => 'value_ar',
            'valueEn' => 'value_en',
            'group' => 'group',
        ];

        $payload = [];
        foreach ($map as $input => $column) {
            if (array_key_exists($input, $validated)) {
                $payload[$column] = $validated[$input];
            }
        }

        $siteSetting->update($payload);

        return response()->json(['data' => $this->transform($siteSetting->fresh())]);
    }

    public function destroy(SiteSetting $siteSetting): JsonResponse
    {
        $siteSetting->delete();

        return response()->json(['message' => 'Deleted']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(SiteSetting $setting): array
    {
        return [
            'id' => $setting->id,
            'key' => $setting->key,
            'valueAr' => $setting->value_ar,
            'valueEn' => $setting->value_en,
            'group' => $setting->group,
            'createdAt' => $setting->created_at?->toIso8601String(),
            'updatedAt' => $setting->updated_at?->toIso8601String(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Enums\LeadershipType;
use App\Http\Controllers\Controller;
use App\Models\LeadershipMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeadershipController extends Controller
{
    public function index(): JsonResponse
    {
        $data = LeadershipMessage::query()
            ->orderBy('type')
            ->get()
            ->map(fn (LeadershipMessage $message) => $this->transform($message))
            ->values();

        return response()->json(['data' => $data]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::enum(LeadershipType::class), Rule::unique('leadership_messages', 'type')],
            'nameAr' => ['required', 'string', 'max:255'],
            'nameEn' => ['required', 'string', 'max:255'],
            'positionAr' => ['required', 'string', 'max:255'],
            'positionEn' => ['required', 'string', 'max:255'],
            'honorificAr' => ['nullable', 'string', 'max:255'],
            'honorificEn' => ['nullable', 'string', 'max:255'],
            'quoteAr' => ['required', 'string'],
            'quoteEn' => ['required', 'string'],
            'paragraphsAr' => ['required', 'array', 'min:1'],
            'paragraphsEn' => ['required', 'array', 'min:1'],
            'imageUrl' => ['nullable', 'string', 'max:500'],
            'imageAltAr' => ['nullable', 'string', 'max:255'],
            'imageAltEn' => ['nullable', 'string', 'max:255'],
        ]);

        $message = LeadershipMessage::query()->create([
            'type' => $validated['type'] instanceof LeadershipType ? $validated['type']->value : $validated['type'],
            'name_ar' => $validated['nameAr'],
            'name_en' => $validated['nameEn'],
            'position_ar' => $validated['positionAr'],
            'position_en' => $validated['positionEn'],
            'honorific_ar' => $validated['honorificAr'] ?? null,
            'honorific_en' => $validated['honorificEn'] ?? null,
            'quote_ar' => $validated['quoteAr'],
            'quote_en' => $validated['quoteEn'],
            'paragraphs_ar' => $validated['paragraphsAr'],
            'paragraphs_en' => $validated['paragraphsEn'],
            'image_url' => $validated['imageUrl'] ?? null,
            'image_alt_ar' => $validated['imageAltAr'] ?? null,
            'image_alt_en' => $validated['imageAltEn'] ?? null,
        ]);

        return response()->json(['data' => $this->transform($message)], 201);
    }

    public function show(LeadershipMessage $leadershipMessage): JsonResponse
    {
        return response()->json(['data' => $this->transform($leadershipMessage)]);
    }

    public function update(Request $request, LeadershipMessage $leadershipMessage): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['sometimes', Rule::enum(LeadershipType::class), Rule::unique('leadership_messages', 'type')->ignore($leadershipMessage->id)],
            'nameAr' => ['sometimes', 'string', 'max:255'],
            'nameEn' => ['sometimes', 'string', 'max:255'],
            'positionAr' => ['sometimes', 'string', 'max:255'],
            'positionEn' => ['sometimes', 'string', 'max:255'],
            'honorificAr' => ['sometimes', 'nullable', 'string', 'max:255'],
            'honorificEn' => ['sometimes', 'nullable', 'string', 'max:255'],
            'quoteAr' => ['sometimes', 'string'],
            'quoteEn' => ['sometimes', 'string'],
            'paragraphsAr' => ['sometimes', 'array'],
            'paragraphsEn' => ['sometimes', 'array'],
            'imageUrl' => ['sometimes', 'nullable', 'string', 'max:500'],
            'imageAltAr' => ['sometimes', 'nullable', 'string', 'max:255'],
            'imageAltEn' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        $payload = [];

        if (array_key_exists('type', $validated)) {
            $payload['type'] = $validated['type'] instanceof LeadershipType
                ? $validated['type']->value
                : $validated['type'];
        }
        if (array_key_exists('nameAr', $validated)) {
            $payload['name_ar'] = $validated['nameAr'];
        }
        if (array_key_exists('nameEn', $validated)) {
            $payload['name_en'] = $validated['nameEn'];
        }
        if (array_key_exists('positionAr', $validated)) {
            $payload['position_ar'] = $validated['positionAr'];
        }
        if (array_key_exists('positionEn', $validated)) {
            $payload['position_en'] = $validated['positionEn'];
        }
        if (array_key_exists('honorificAr', $validated)) {
            $payload['honorific_ar'] = $validated['honorificAr'];
        }
        if (array_key_exists('honorificEn', $validated)) {
            $payload['honorific_en'] = $validated['honorificEn'];
        }
        if (array_key_exists('quoteAr', $validated)) {
            $payload['quote_ar'] = $validated['quoteAr'];
        }
        if (array_key_exists('quoteEn', $validated)) {
            $payload['quote_en'] = $validated['quoteEn'];
        }
        if (array_key_exists('paragraphsAr', $validated)) {
            $payload['paragraphs_ar'] = $validated['paragraphsAr'];
        }
        if (array_key_exists('paragraphsEn', $validated)) {
            $payload['paragraphs_en'] = $validated['paragraphsEn'];
        }
        if (array_key_exists('imageUrl', $validated)) {
            $payload['image_url'] = $validated['imageUrl'];
        }
        if (array_key_exists('imageAltAr', $validated)) {
            $payload['image_alt_ar'] = $validated['imageAltAr'];
        }
        if (array_key_exists('imageAltEn', $validated)) {
            $payload['image_alt_en'] = $validated['imageAltEn'];
        }

        $leadershipMessage->update($payload);

        return response()->json(['data' => $this->transform($leadershipMessage->fresh())]);
    }

    public function destroy(LeadershipMessage $leadershipMessage): JsonResponse
    {
        $leadershipMessage->delete();

        return response()->json(['message' => 'Deleted']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(LeadershipMessage $message): array
    {
        return [
            'id' => $message->id,
            'type' => $message->type,
            'nameAr' => $message->name_ar,
            'nameEn' => $message->name_en,
            'positionAr' => $message->position_ar,
            'positionEn' => $message->position_en,
            'honorificAr' => $message->honorific_ar,
            'honorificEn' => $message->honorific_en,
            'quoteAr' => $message->quote_ar,
            'quoteEn' => $message->quote_en,
            'paragraphsAr' => $message->paragraphs_ar,
            'paragraphsEn' => $message->paragraphs_en,
            'imageUrl' => $message->image_url,
            'imageAltAr' => $message->image_alt_ar,
            'imageAltEn' => $message->image_alt_en,
            'createdAt' => $message->created_at?->toIso8601String(),
            'updatedAt' => $message->updated_at?->toIso8601String(),
        ];
    }
}

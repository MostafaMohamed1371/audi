<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortalContribution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PortalContributionController extends Controller
{
    /**
     * @return array<string, mixed>
     */
    private function transform(PortalContribution $item): array
    {
        return [
            'id' => $item->id,
            'type' => $item->type,
            'email' => $item->email,
            'payload' => $item->payload,
            'status' => $item->status,
            'createdAt' => $item->created_at?->toIso8601String(),
            'updatedAt' => $item->updated_at?->toIso8601String(),
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = PortalContribution::query()->latest();

        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $paginator = $query->paginate($limit);

        return response()->json([
            'data' => collect($paginator->items())->map(fn (PortalContribution $item) => $this->transform($item))->values(),
            'meta' => [
                'page' => $paginator->currentPage(),
                'limit' => $paginator->perPage(),
                'total' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
            ],
        ]);
    }

    public function show(PortalContribution $portalContribution): JsonResponse
    {
        return response()->json(['data' => $this->transform($portalContribution)]);
    }

    public function update(Request $request, PortalContribution $portalContribution): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:new,reviewing,approved,rejected'],
        ]);

        $portalContribution->update(['status' => $validated['status']]);

        return response()->json([
            'data' => [
                'id' => $portalContribution->id,
                'status' => $portalContribution->status,
            ],
        ]);
    }

    public function destroy(PortalContribution $portalContribution): JsonResponse
    {
        $portalContribution->delete();

        return response()->json(['message' => 'Deleted']);
    }
}

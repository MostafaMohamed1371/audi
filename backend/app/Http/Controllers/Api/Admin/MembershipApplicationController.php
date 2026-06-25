<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\UpdateMembershipApplicationStatusRequest;
use App\Http\Resources\Api\Admin\MembershipApplicationResource;
use App\Models\MembershipApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MembershipApplicationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = MembershipApplication::query()->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('organization_name', 'like', "%{$search}%")
                    ->orWhere('contact_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $paginator = $query->paginate($limit);

        return MembershipApplicationResource::collection($paginator)->additional([
            'meta' => [
                'page' => $paginator->currentPage(),
                'limit' => $paginator->perPage(),
                'total' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
            ],
        ]);
    }

    public function show(MembershipApplication $membershipApplication): MembershipApplicationResource
    {
        return new MembershipApplicationResource($membershipApplication);
    }

    public function update(
        UpdateMembershipApplicationStatusRequest $request,
        MembershipApplication $membershipApplication,
    ): MembershipApplicationResource {
        $membershipApplication->update([
            'status' => $request->validated('status'),
        ]);

        return new MembershipApplicationResource($membershipApplication->fresh());
    }

    public function destroy(MembershipApplication $membershipApplication): JsonResponse
    {
        $membershipApplication->delete();

        return response()->json(['message' => 'Deleted']);
    }
}

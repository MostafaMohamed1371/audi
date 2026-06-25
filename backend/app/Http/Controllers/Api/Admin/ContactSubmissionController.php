<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\UpdateContactSubmissionStatusRequest;
use App\Http\Resources\Api\Admin\ContactSubmissionResource;
use App\Models\ContactSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContactSubmissionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = ContactSubmission::query()->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $paginator = $query->paginate($limit);

        return ContactSubmissionResource::collection($paginator)->additional([
            'meta' => [
                'page' => $paginator->currentPage(),
                'limit' => $paginator->perPage(),
                'total' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
            ],
        ]);
    }

    public function show(ContactSubmission $contactSubmission): ContactSubmissionResource
    {
        return new ContactSubmissionResource($contactSubmission);
    }

    public function update(
        UpdateContactSubmissionStatusRequest $request,
        ContactSubmission $contactSubmission,
    ): ContactSubmissionResource {
        $contactSubmission->update([
            'status' => $request->validated('status'),
        ]);

        return new ContactSubmissionResource($contactSubmission->fresh());
    }

    public function destroy(ContactSubmission $contactSubmission): JsonResponse
    {
        $contactSubmission->delete();

        return response()->json(['message' => 'Deleted']);
    }
}

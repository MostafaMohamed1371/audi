<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Enums\JobApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Support\ImageUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JobApplicationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = JobApplication::query()->with('jobOpening')->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $paginator = $query->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (JobApplication $a) => $this->transform($a))->values(),
            'meta' => [
                'page' => $paginator->currentPage(),
                'limit' => $paginator->perPage(),
                'total' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
            ],
        ]);
    }

    public function show(JobApplication $jobApplication): JsonResponse
    {
        return response()->json(['data' => $this->transform($jobApplication->load('jobOpening'))]);
    }

    public function update(Request $request, JobApplication $jobApplication): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(JobApplicationStatus::class)],
        ]);

        $jobApplication->update(['status' => $validated['status']]);

        return response()->json(['data' => $this->transform($jobApplication->fresh()->load('jobOpening'))]);
    }

    public function destroy(JobApplication $jobApplication): JsonResponse
    {
        $jobApplication->delete();

        return response()->json(['message' => 'Deleted']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(JobApplication $a): array
    {
        return [
            'id' => $a->id,
            'jobOpeningId' => $a->job_opening_id,
            'jobTitle' => $a->jobOpening?->title_ar,
            'fullName' => $a->full_name,
            'email' => $a->email,
            'phone' => $a->phone,
            'coverLetter' => $a->cover_letter,
            'cvUrl' => ImageUrl::api($a->cv_url),
            'status' => $a->status instanceof JobApplicationStatus ? $a->status->value : $a->status,
            'createdAt' => $a->created_at?->toIso8601String(),
        ];
    }
}

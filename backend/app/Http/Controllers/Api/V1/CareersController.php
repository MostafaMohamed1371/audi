<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\JobApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreJobApplicationRequest;
use App\Models\JobApplication;
use App\Models\JobOpening;
use Illuminate\Http\JsonResponse;

class CareersController extends Controller
{
    public function index(): JsonResponse
    {
        $items = JobOpening::query()
            ->where('is_published', true)
            ->ordered()
            ->get()
            ->map(fn (JobOpening $opening) => $this->transform($opening))
            ->values();

        return response()->json(['data' => $items]);
    }

    public function show(JobOpening $jobOpening): JsonResponse
    {
        if (! $jobOpening->is_published) {
            return response()->json(['message' => 'Job opening not found.'], 404);
        }

        return response()->json(['data' => $this->transform($jobOpening)]);
    }

    public function apply(StoreJobApplicationRequest $request): JsonResponse
    {
        $application = JobApplication::query()->create([
            'job_opening_id' => $request->validated('jobOpeningId'),
            'full_name' => $request->validated('fullName'),
            'email' => $request->validated('email'),
            'phone' => $request->validated('phone'),
            'cover_letter' => $request->validated('coverLetter'),
            'cv_url' => $request->validated('cvUrl'),
            'status' => JobApplicationStatus::New,
        ]);

        $message = app()->getLocale() === 'ar'
            ? 'تم استلام طلبك بنجاح. سنتواصل معك قريباً.'
            : 'Your application was received successfully. We will contact you soon.';

        return response()->json([
            'message' => $message,
            'id' => $application->id,
        ], 201);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(JobOpening $opening): array
    {
        $isAr = app()->getLocale() === 'ar';

        return [
            'id' => $opening->id,
            'title' => $isAr ? $opening->title_ar : $opening->title_en,
            'location' => $isAr ? $opening->location_ar : $opening->location_en,
            'employmentType' => $opening->employment_type,
            'summary' => $isAr ? $opening->summary_ar : $opening->summary_en,
            'description' => ($isAr ? $opening->description_ar : $opening->description_en) ?? [],
            'publishedDate' => $opening->created_at?->toDateString(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\ContactSubmissionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreContactSubmissionRequest;
use App\Models\ContactSubmission;
use App\Services\ContactInfoService;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function __construct(private readonly ContactInfoService $contactInfo) {}

    public function show(): JsonResponse
    {
        return response()->json($this->contactInfo->getPublicPayload());
    }

    public function store(StoreContactSubmissionRequest $request): JsonResponse
    {
        $submission = ContactSubmission::query()->create([
            'name' => $request->validated('name'),
            'phone' => $request->validated('phone'),
            'email' => $request->validated('email'),
            'message' => $request->validated('message'),
            'status' => ContactSubmissionStatus::New,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $message = app()->getLocale() === 'ar'
            ? 'تم إرسال رسالتك بنجاح. سنتواصل معك قريباً.'
            : 'Your message was sent successfully. We will contact you soon.';

        return response()->json([
            'message' => $message,
            'id' => $submission->id,
        ], 201);
    }
}

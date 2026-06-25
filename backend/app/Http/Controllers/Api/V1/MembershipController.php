<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\MembershipApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreMembershipApplicationRequest;
use App\Models\MembershipApplication;
use Illuminate\Http\JsonResponse;

class MembershipController extends Controller
{
    public function store(StoreMembershipApplicationRequest $request): JsonResponse
    {
        $application = MembershipApplication::query()->create([
            'organization_name' => $request->validated('organization_name'),
            'contact_name' => $request->validated('contact_name'),
            'email' => $request->validated('email'),
            'phone' => $request->validated('phone'),
            'country_code' => $request->validated('country_code'),
            'city' => $request->validated('city'),
            'message' => $request->validated('message'),
            'status' => MembershipApplicationStatus::New,
        ]);

        $message = app()->getLocale() === 'ar'
            ? 'تم استلام طلب العضوية بنجاح. سنتواصل معكم قريباً.'
            : 'Your membership application was received successfully. We will contact you soon.';

        return response()->json([
            'message' => $message,
            'id' => $application->id,
        ], 201);
    }
}

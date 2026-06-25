<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\UpdateContactInfoRequest;
use App\Services\ContactInfoService;
use Illuminate\Http\JsonResponse;

class ContactInfoController extends Controller
{
    public function __construct(private readonly ContactInfoService $contactInfo) {}

    public function show(): JsonResponse
    {
        return response()->json(['data' => $this->contactInfo->getAdminPayload()]);
    }

    public function update(UpdateContactInfoRequest $request): JsonResponse
    {
        return response()->json([
            'data' => $this->contactInfo->updateFromAdmin($request->validated()),
        ]);
    }
}

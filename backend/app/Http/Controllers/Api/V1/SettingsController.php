<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\SiteSettingsService;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    public function __construct(private readonly SiteSettingsService $settings) {}

    public function show(): JsonResponse
    {
        return response()->json(['data' => $this->settings->getPublicPayload()]);
    }
}

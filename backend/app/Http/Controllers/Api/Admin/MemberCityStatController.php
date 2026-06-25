<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\UpdateMemberCityStatsRequest;
use App\Services\MemberCities\MemberCityStatService;
use Illuminate\Http\JsonResponse;

class MemberCityStatController extends Controller
{
    public function __construct(private readonly MemberCityStatService $stats) {}

    public function index(): JsonResponse
    {
        return response()->json(['items' => $this->stats->getAdminStats()]);
    }

    public function update(UpdateMemberCityStatsRequest $request): JsonResponse
    {
        $this->stats->updateStats($request->validated('items'));

        return response()->json(['items' => $this->stats->getAdminStats()]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Strategy\StrategyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StrategyController extends Controller
{
    public function __construct(private readonly StrategyService $strategy) {}

    public function strategy2025(Request $request): JsonResponse
    {
        $locale = $request->attributes->get('locale', app()->getLocale());

        return response()->json($this->strategy->getStrategy2025($locale));
    }

    public function focusAreas(Request $request): JsonResponse
    {
        $locale = $request->attributes->get('locale', app()->getLocale());

        return response()->json($this->strategy->getFocusAreas($locale));
    }

    public function focusArea(Request $request, string $slug): JsonResponse
    {
        $locale = $request->attributes->get('locale', app()->getLocale());
        $payload = $this->strategy->getFocusArea($slug, $locale);

        if (! $payload) {
            return response()->json(['message' => 'Focus area not found.'], 404);
        }

        return response()->json($payload);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\MemberCities\MemberCitiesMapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberCitiesController extends Controller
{
    public function __construct(private readonly MemberCitiesMapService $mapService) {}

    public function show(Request $request): JsonResponse
    {
        $locale = $request->attributes->get('locale', app()->getLocale());

        return response()->json($this->mapService->getFullPayload($locale));
    }

    public function countriesGeoJson(): JsonResponse
    {
        return response()->json($this->mapService->getCountriesGeoJson());
    }

    public function citiesGeoJson(Request $request): JsonResponse
    {
        $locale = $request->attributes->get('locale', app()->getLocale());

        return response()->json($this->mapService->getCitiesGeoJson($locale));
    }
}

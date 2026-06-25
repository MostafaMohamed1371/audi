<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\About\AboutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function __construct(private readonly AboutService $about) {}

    public function institute(Request $request): JsonResponse
    {
        $locale = $request->attributes->get('locale', app()->getLocale());

        return response()->json($this->about->getInstitute($locale));
    }

    public function visionMission(Request $request): JsonResponse
    {
        $locale = $request->attributes->get('locale', app()->getLocale());

        return response()->json($this->about->getVisionMission($locale));
    }

    public function leadership(Request $request, string $type): JsonResponse
    {
        $locale = $request->attributes->get('locale', app()->getLocale());
        $payload = $this->about->getLeadership($type, $locale);

        if (! $payload) {
            return response()->json(['message' => 'Leadership message not found.'], 404);
        }

        return response()->json($payload);
    }

    public function advisoryBoard(Request $request): JsonResponse
    {
        $locale = $request->attributes->get('locale', app()->getLocale());

        return response()->json($this->about->getAdvisoryBoard($locale));
    }

    public function team(Request $request): JsonResponse
    {
        $locale = $request->attributes->get('locale', app()->getLocale());

        return response()->json($this->about->getTeam($locale));
    }

    public function structure(Request $request): JsonResponse
    {
        $locale = $request->attributes->get('locale', app()->getLocale());

        return response()->json($this->about->getStructure($locale));
    }

    public function partners(Request $request): JsonResponse
    {
        $locale = $request->attributes->get('locale', app()->getLocale());

        return response()->json($this->about->getPartners($locale));
    }
}

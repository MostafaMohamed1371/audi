<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\JsonResponse;

class MemberCountryController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Country::query()
            ->orderBy('name_en')
            ->get(['code_a2', 'code_a3', 'name_en', 'name_ar'])
            ->map(fn (Country $country) => [
                'codeA2' => $country->code_a2,
                'codeA3' => $country->code_a3,
                'nameEn' => $country->name_en,
                'nameAr' => $country->name_ar,
            ])
            ->values();

        return response()->json(['data' => $data]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ImportMemberCitiesRequest;
use App\Http\Requests\Api\Admin\StoreMemberCityRequest;
use App\Http\Requests\Api\Admin\UpdateMemberCityRequest;
use App\Http\Resources\Api\Admin\MemberCityResource;
use App\Models\MemberCity;
use App\Services\MemberCities\MemberCityImporter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MemberCityController extends Controller
{
    public function __construct(private readonly MemberCityImporter $importer) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = MemberCity::query()->latest();

        if ($countryCode = $request->query('countryCode', $request->query('country_code'))) {
            $query->where('country_code', strtoupper((string) $countryCode));
        }

        if ($request->has('isActive')) {
            $query->where('is_active', filter_var($request->query('isActive'), FILTER_VALIDATE_BOOLEAN));
        } elseif ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($search = $request->query('search')) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name_ar', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        $paginator = $query->paginate($limit);

        return MemberCityResource::collection($paginator)->additional([
            'meta' => [
                'page' => $paginator->currentPage(),
                'limit' => $paginator->perPage(),
                'total' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(StoreMemberCityRequest $request): JsonResponse
    {
        $city = MemberCity::query()->create($request->validated());

        return (new MemberCityResource($city))
            ->response()
            ->setStatusCode(201);
    }

    public function show(MemberCity $memberCity): MemberCityResource
    {
        return new MemberCityResource($memberCity);
    }

    public function update(UpdateMemberCityRequest $request, MemberCity $memberCity): MemberCityResource
    {
        $memberCity->update($request->validated());

        return new MemberCityResource($memberCity->fresh());
    }

    public function destroy(Request $request, MemberCity $memberCity): JsonResponse
    {
        if ($request->boolean('force')) {
            $memberCity->delete();
        } else {
            $memberCity->update(['is_active' => false]);
        }

        return response()->json(['message' => 'Deleted']);
    }

    public function import(ImportMemberCitiesRequest $request): JsonResponse
    {
        $result = $this->importer->upsertCities(
            $request->validated('cities'),
            $request->validated('upsert_by') ?? ['country_code', 'name_en'],
        );

        return response()->json($result, 201);
    }

    public function importFromFile(): JsonResponse
    {
        $result = $this->importer->importFromGeoJsonFile();

        return response()->json($result, 201);
    }
}

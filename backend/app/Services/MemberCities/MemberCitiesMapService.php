<?php

declare(strict_types=1);

namespace App\Services\MemberCities;

use App\Models\Country;
use App\Models\MemberCity;
use Illuminate\Support\Facades\File;

class MemberCitiesMapService
{
    private const COUNTRIES_FILE = 'geojson/arab-countries.geojson';

    public function __construct(private readonly MemberCityStatService $stats) {}

    public function getFullPayload(?string $locale = null): array
    {
        return [
            'stats' => $this->stats->getPublicStats($locale),
            'countriesGeoJson' => $this->getCountriesGeoJson(),
            'citiesGeoJson' => $this->getCitiesGeoJson($locale),
        ];
    }

    public function getCountriesGeoJson(): array
    {
        if (Country::query()->exists()) {
            return $this->buildCountriesCollectionFromDatabase();
        }

        return $this->readCountriesFile();
    }

    public function getCitiesGeoJson(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $isAr = $locale === 'ar';

        if (! MemberCity::query()->where('is_active', true)->exists()) {
            return $this->readCitiesFile($isAr);
        }

        return $this->buildCitiesCollectionFromDatabase($isAr);
    }

    private function buildCountriesCollectionFromDatabase(): array
    {
        $features = Country::query()
            ->whereNotNull('geojson')
            ->orderBy('name_en')
            ->get()
            ->map(fn (Country $country) => [
                'type' => 'Feature',
                'properties' => [
                    'name' => $country->name_en,
                    'formal_en_name' => $country->name_en,
                    'code_a2' => $country->code_a2,
                    'code_a3' => $country->code_a3,
                ],
                'geometry' => $country->geojson,
            ])
            ->values()
            ->all();

        return [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
    }

    private function buildCitiesCollectionFromDatabase(bool $isAr): array
    {
        $features = MemberCity::query()
            ->where('is_active', true)
            ->orderBy('country_code')
            ->orderBy($isAr ? 'name_ar' : 'name_en')
            ->get()
            ->map(fn (MemberCity $city) => $this->cityToFeature($city, $isAr))
            ->values()
            ->all();

        return [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
    }

    private function cityToFeature(MemberCity $city, bool $isAr): array
    {
        $longitude = (float) $city->longitude;
        $latitude = (float) $city->latitude;

        return [
            'type' => 'Feature',
            'ccode' => $city->country_code,
            'properties' => [
                'Name' => $isAr ? $city->name_ar : $city->name_en,
                'Info' => $isAr ? ($city->info_ar ?? '') : ($city->info_en ?? ''),
                'Image' => $city->image_url,
                'x' => $longitude,
                'y' => $latitude,
            ],
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [$longitude, $latitude],
            ],
        ];
    }

    private function readCountriesFile(): array
    {
        $path = storage_path('app/'.self::COUNTRIES_FILE);

        if (! File::exists($path)) {
            return ['type' => 'FeatureCollection', 'features' => []];
        }

        $data = json_decode(File::get($path), true);

        return is_array($data) ? $data : ['type' => 'FeatureCollection', 'features' => []];
    }

    private function readCitiesFile(bool $isAr): array
    {
        $path = storage_path('app/geojson/member-cities.geojson');

        if (! File::exists($path)) {
            return ['type' => 'FeatureCollection', 'features' => []];
        }

        $data = json_decode(File::get($path), true);

        if (! is_array($data) || ! isset($data['features'])) {
            return ['type' => 'FeatureCollection', 'features' => []];
        }

        if ($isAr) {
            return $data;
        }

        $cityNames = MemberCity::query()
            ->get(['country_code', 'name_ar', 'name_en'])
            ->mapWithKeys(fn (MemberCity $city) => [
                $city->country_code.'|'.$city->name_ar => $city->name_en,
            ]);

        $data['features'] = array_map(function (array $feature) use ($cityNames) {
            $ccode = $feature['ccode'] ?? '';
            $nameAr = $feature['properties']['Name'] ?? '';
            $english = $cityNames->get($ccode.'|'.$nameAr);

            if ($english) {
                $feature['properties']['Name'] = $english;
            }

            return $feature;
        }, $data['features']);

        return $data;
    }
}

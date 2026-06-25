<?php

declare(strict_types=1);

namespace App\Services\MemberCities;

use App\Models\Country;
use App\Models\MemberCity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MemberCityImporter
{
    /** Country codes present in member-cities.geojson but absent from arab-countries.geojson */
    private const EXTRA_COUNTRY_NAMES = [
        'KM' => ['en' => 'Comoros', 'ar' => 'جزر القمر'],
        'MR' => ['en' => 'Mauritania', 'ar' => 'موريتانيا'],
        'SD' => ['en' => 'Sudan', 'ar' => 'السودان'],
        'SO' => ['en' => 'Somalia', 'ar' => 'الصومال'],
    ];

    /**
     * @return array{imported: int, updated: int, skipped: int}
     */
    public function importFromGeoJsonFile(?string $path = null): array
    {
        $path ??= storage_path('app/geojson/member-cities.geojson');

        if (! File::exists($path)) {
            return ['imported' => 0, 'updated' => 0, 'skipped' => 0];
        }

        $data = json_decode(File::get($path), true);
        $features = $data['features'] ?? [];

        $this->ensureCountriesFromCityFeatures($features);

        $cities = array_map(fn (array $feature) => $this->featureToCityPayload($feature), $features);

        return $this->upsertCities($cities, ['country_code', 'name_ar']);
    }

    /**
     * @param  array<int, array<string, mixed>>  $cities
     * @param  array<int, string>  $upsertBy
     * @return array{imported: int, updated: int, skipped: int}
     */
    public function upsertCities(array $cities, array $upsertBy = ['country_code', 'name_en']): array
    {
        $imported = 0;
        $updated = 0;
        $skipped = 0;

        DB::transaction(function () use ($cities, $upsertBy, &$imported, &$updated, &$skipped) {
            foreach ($cities as $city) {
                if (! $this->isValidCityPayload($city)) {
                    $skipped++;

                    continue;
                }

                $this->ensureCountryExists((string) $city['country_code']);

                $query = MemberCity::query();

                foreach ($upsertBy as $field) {
                    $query->where($field, $city[$field]);
                }

                $existing = $query->first();

                if ($existing) {
                    $existing->update($city);
                    $updated++;
                } else {
                    MemberCity::query()->create($city);
                    $imported++;
                }
            }
        });

        return compact('imported', 'updated', 'skipped');
    }

    /**
     * @param  array<int, array<string, mixed>>  $features
     */
    public function ensureCountriesFromCityFeatures(array $features): int
    {
        $codes = collect($features)
            ->map(fn (array $feature) => strtoupper((string) ($feature['ccode'] ?? '')))
            ->filter(fn (string $code) => strlen($code) === 2)
            ->unique()
            ->values();

        $created = 0;

        foreach ($codes as $code) {
            if ($this->ensureCountryExists($code)) {
                $created++;
            }
        }

        return $created;
    }

    private function ensureCountryExists(string $code): bool
    {
        if (Country::query()->whereKey($code)->exists()) {
            return false;
        }

        $names = self::EXTRA_COUNTRY_NAMES[$code] ?? ['en' => $code, 'ar' => null];

        Country::query()->create([
            'code_a2' => $code,
            'name_en' => $names['en'],
            'name_ar' => $names['ar'],
            'geojson' => null,
        ]);

        return true;
    }

    /**
     * @return array{imported: int, updated: int, skipped: int}
     */
    public function importCountriesFromGeoJsonFile(?string $path = null): array
    {
        $path ??= storage_path('app/geojson/arab-countries.geojson');

        if (! File::exists($path)) {
            return ['imported' => 0, 'updated' => 0, 'skipped' => 0];
        }

        $data = json_decode(File::get($path), true);
        $features = $data['features'] ?? [];

        $imported = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($features as $feature) {
            $properties = $feature['properties'] ?? [];
            $codeA2 = strtoupper((string) ($properties['code_a2'] ?? ''));

            if (strlen($codeA2) !== 2 || ! isset($feature['geometry'])) {
                $skipped++;

                continue;
            }

            $payload = [
                'code_a3' => $properties['code_a3'] ?? null,
                'name_en' => $properties['name'] ?? $properties['formal_en_name'] ?? $codeA2,
                'name_ar' => $properties['name_ar'] ?? null,
                'geojson' => $feature['geometry'],
            ];

            $country = Country::query()->find($codeA2);

            if ($country) {
                $country->update($payload);
                $updated++;
            } else {
                Country::query()->create(array_merge(['code_a2' => $codeA2], $payload));
                $imported++;
            }
        }

        return compact('imported', 'updated', 'skipped');
    }

    /**
     * @param  array<string, mixed>  $feature
     * @return array<string, mixed>
     */
    private function featureToCityPayload(array $feature): array
    {
        $properties = $feature['properties'] ?? [];
        $coordinates = $feature['geometry']['coordinates'] ?? [0, 0];
        $name = (string) ($properties['Name'] ?? '');

        return [
            'country_code' => strtoupper((string) ($feature['ccode'] ?? '')),
            'name_ar' => $name,
            'name_en' => $name,
            'latitude' => (float) ($properties['y'] ?? $coordinates[1] ?? 0),
            'longitude' => (float) ($properties['x'] ?? $coordinates[0] ?? 0),
            'info_ar' => (string) ($properties['Info'] ?? ''),
            'info_en' => (string) ($properties['Info'] ?? ''),
            'image_url' => $properties['Image'] ?: null,
            'is_active' => true,
        ];
    }

    /**
     * @param  array<string, mixed>  $city
     */
    private function isValidCityPayload(array $city): bool
    {
        return strlen((string) ($city['country_code'] ?? '')) === 2
            && ! empty($city['name_ar'])
            && ! empty($city['name_en'])
            && is_numeric($city['latitude'])
            && is_numeric($city['longitude']);
    }
}

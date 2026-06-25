<?php

namespace Database\Seeders;

use App\Services\MemberCities\MemberCityImporter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class MemberCitiesGeoJsonSeeder extends Seeder
{
    private const GEOJSON_SOURCES = [
        'arab-countries.geojson' => 'https://audi-ten.vercel.app/data/arab-countries.geojson',
        'member-cities.geojson' => 'https://audi-ten.vercel.app/data/member-cities.geojson',
    ];

    public function run(): void
    {
        $this->ensureGeoJsonFiles();
        $importer = app(MemberCityImporter::class);

        $countries = $importer->importCountriesFromGeoJsonFile();
        $cities = $importer->importFromGeoJsonFile();

        $this->command?->info(sprintf(
            'Countries: %d imported, %d updated. Cities: %d imported, %d updated, %d skipped.',
            $countries['imported'],
            $countries['updated'],
            $cities['imported'],
            $cities['updated'],
            $cities['skipped'],
        ));
    }

    private function ensureGeoJsonFiles(): void
    {
        $directory = storage_path('app/geojson');
        File::ensureDirectoryExists($directory);

        foreach (self::GEOJSON_SOURCES as $filename => $url) {
            $path = $directory.'/'.$filename;

            if (File::exists($path) && File::size($path) > 0) {
                continue;
            }

            $this->command?->warn("Downloading {$filename}...");

            $response = Http::timeout(120)->get($url);

            if ($response->successful()) {
                File::put($path, $response->body());
            }
        }
    }
}

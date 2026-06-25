<?php

declare(strict_types=1);

namespace App\Services\Programs;

use App\Models\AboutContent;
use App\Models\DirectoryCity;
use App\Models\DirectoryOrganization;
use App\Models\DirectoryProject;
use App\Models\DirectoryPublication;
use App\Models\ProgramSection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class DirectoryService
{
    private const TABS = ['cities', 'projects', 'organizations', 'publications'];

    /**
     * @return array<string, mixed>
     */
    public function getDirectory(string $tab, ?string $locale = null, array $filters = []): array
    {
        if (! in_array($tab, self::TABS, true)) {
            throw new InvalidArgumentException("Unknown directory tab: {$tab}");
        }

        $isAr = ($locale ?? app()->getLocale()) === 'ar';
        $limit = min(max((int) ($filters['limit'] ?? 20), 1), 100);
        $meta = $this->directoryUiMeta($isAr);

        $query = $this->queryForTab($tab);
        $this->applyFilters($query, $tab, $filters, $isAr);

        if ($search = $filters['search'] ?? null) {
            $this->applySearch($query, $tab, $search, $isAr);
        }

        $paginator = $query
            ->ordered()
            ->paginate($limit, ['*'], 'page', max(1, (int) ($filters['page'] ?? 1)));

        return [
            'tab' => $tab,
            'meta' => [
                'page' => $paginator->currentPage(),
                'limit' => $paginator->perPage(),
                'total' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
            ],
            'ui' => $meta,
            'data' => collect($paginator->items())
                ->map(fn (Model $row) => $this->mapRow($tab, $row, $isAr))
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function directoryUiMeta(bool $isAr): array
    {
        $section = ProgramSection::query()
            ->whereHas('program', fn ($q) => $q->where('slug', 'urban-policies'))
            ->where('tab_key', 'developmentPortal')
            ->first();

        $body = $isAr ? ($section?->body_ar ?? []) : ($section?->body_en ?? []);
        $directory = is_array($body) ? ($body['directory'] ?? []) : [];

        if (isset($directory['rows'])) {
            unset($directory['rows']);
        }

        return $directory;
    }

    private function queryForTab(string $tab): Builder
    {
        return match ($tab) {
            'cities' => DirectoryCity::query(),
            'projects' => DirectoryProject::query(),
            'organizations' => DirectoryOrganization::query(),
            'publications' => DirectoryPublication::query(),
        };
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function applyFilters(Builder $query, string $tab, array $filters, bool $isAr): void
    {
        if ($tab === 'cities') {
            if ($country = $filters['country'] ?? $filters['countryCode'] ?? null) {
                $query->where('country_code', strtoupper((string) $country));
            }

            if ($citySize = $filters['citySize'] ?? null) {
                $query->where('city_size', $citySize);
            }
        }
    }

    private function applySearch(Builder $query, string $tab, string $search, bool $isAr): void
    {
        $like = '%'.$search.'%';

        match ($tab) {
            'cities' => $query->where(function (Builder $builder) use ($like, $isAr) {
                $builder
                    ->where($isAr ? 'name_ar' : 'name_en', 'like', $like)
                    ->orWhere('description_ar', 'like', $like)
                    ->orWhere('description_en', 'like', $like);
            }),
            'projects' => $query->where(function (Builder $builder) use ($like, $isAr) {
                $builder
                    ->where($isAr ? 'city_ar' : 'city_en', 'like', $like)
                    ->orWhere($isAr ? 'country_ar' : 'country_en', 'like', $like);
            }),
            'organizations', 'publications' => $query->where(function (Builder $builder) use ($like, $isAr) {
                $builder
                    ->where($isAr ? 'name_ar' : 'name_en', 'like', $like)
                    ->orWhere('description_ar', 'like', $like)
                    ->orWhere('description_en', 'like', $like);
            }),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function mapRow(string $tab, Model $row, bool $isAr): array
    {
        return match ($tab) {
            'cities' => [
                'number' => $row->number,
                'name' => $isAr ? $row->name_ar : $row->name_en,
                'description' => $isAr ? $row->description_ar : $row->description_en,
                'countryCode' => $row->country_code,
                'citySize' => $row->city_size,
            ],
            'projects' => [
                'number' => $row->number,
                'city' => $isAr ? $row->city_ar : $row->city_en,
                'country' => $isAr ? $row->country_ar : $row->country_en,
                'startDate' => $row->start_date,
                'endDate' => $row->end_date,
            ],
            'organizations', 'publications' => [
                'number' => $row->number,
                'name' => $isAr ? $row->name_ar : $row->name_en,
                'description' => $isAr ? $row->description_ar : $row->description_en,
            ],
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Programs;

use App\Models\AboutContent;
use App\Models\DirectoryCity;
use App\Models\DirectoryDiscussion;
use App\Models\DirectoryOrganization;
use App\Models\DirectoryProject;
use App\Models\DirectoryPublication;
use App\Models\ProgramSection;
use App\Support\ImageUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

class DirectoryService
{
    private const TABS = ['cities', 'projects', 'organizations', 'publications'];

    /**
     * @return array<string, mixed>
     */
    public function getDirectory(string $tab, ?string $locale = null, array $filters = []): array
    {
        $this->assertTab($tab);

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
    public function getItem(string $tab, string $number, ?string $locale = null): array
    {
        $this->assertTab($tab);

        $isAr = ($locale ?? app()->getLocale()) === 'ar';
        $row = $this->queryForTab($tab)->where('number', $number)->first();

        if (! $row) {
            throw new ModelNotFoundException("Directory item [{$tab}/{$number}] not found.");
        }

        $ui = $this->directoryUiMeta($isAr);

        return [
            'tab' => $tab,
            'number' => $row->number,
            'item' => $this->mapRow($tab, $row, $isAr, includeDetail: true),
            'discussions' => $this->mapDiscussions($tab, $number, $isAr),
            'ui' => [
                'discussionTitle' => $ui['discussionTitle'] ?? null,
                'addCommentLabel' => $ui['addCommentLabel'] ?? null,
                'authorNameLabel' => $ui['authorNameLabel'] ?? null,
                'commentBodyLabel' => $ui['commentBodyLabel'] ?? null,
                'submitCommentLabel' => $ui['submitCommentLabel'] ?? null,
                'backToListLabel' => $ui['backToListLabel'] ?? null,
                'shareLabel' => $ui['shareLabel'] ?? null,
                'downloadLabel' => $ui['downloadLabel'] ?? null,
                'addressLabel' => $ui['addressLabel'] ?? null,
                'sourceLabel' => $ui['sourceLabel'] ?? null,
                'relatedProjectsTitle' => $ui['relatedProjectsTitle'] ?? null,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function storeDiscussion(
        string $tab,
        string $number,
        string $authorName,
        string $body,
        ?string $locale = null,
    ): array {
        $this->assertTab($tab);

        $row = $this->queryForTab($tab)->where('number', $number)->first();
        if (! $row) {
            throw new ModelNotFoundException("Directory item [{$tab}/{$number}] not found.");
        }

        $isAr = ($locale ?? app()->getLocale()) === 'ar';

        $discussion = DirectoryDiscussion::query()->create([
            'directory_type' => $tab,
            'directory_number' => $number,
            'author_name_ar' => $isAr ? $authorName : $authorName,
            'author_name_en' => $isAr ? $authorName : $authorName,
            'body_ar' => $isAr ? $body : $body,
            'body_en' => $isAr ? $body : $body,
            'is_approved' => false,
            'sort_order' => DirectoryDiscussion::query()
                ->where('directory_type', $tab)
                ->where('directory_number', $number)
                ->count(),
        ]);

        return $this->mapDiscussion($discussion, $isAr);
    }

    /**
     * @return array<string, mixed>
     */
    private function directoryUiMeta(bool $isAr): array
    {
        $section = ProgramSection::query()
            ->whereHas('program', fn ($q) => $q->where('slug', 'urban-policies'))
            ->where('tab_key', 'developmentPortal')
            ->with('details')
            ->first();

        $detail = $section?->details;
        $body = $isAr ? ($detail?->body_ar ?? []) : ($detail?->body_en ?? []);
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

    private function assertTab(string $tab): void
    {
        if (! in_array($tab, self::TABS, true)) {
            throw new InvalidArgumentException("Unknown directory tab: {$tab}");
        }
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
     * @return array<int, array<string, mixed>>
     */
    private function mapDiscussions(string $tab, string $number, bool $isAr): array
    {
        return DirectoryDiscussion::query()
            ->where('directory_type', $tab)
            ->where('directory_number', $number)
            ->where('is_approved', true)
            ->ordered()
            ->get()
            ->map(fn (DirectoryDiscussion $discussion) => $this->mapDiscussion($discussion, $isAr))
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function mapDiscussion(DirectoryDiscussion $discussion, bool $isAr): array
    {
        return [
            'id' => $discussion->id,
            'author' => $isAr ? $discussion->author_name_ar : $discussion->author_name_en,
            'body' => $isAr ? $discussion->body_ar : $discussion->body_en,
            'createdAt' => $discussion->created_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function mapRow(string $tab, Model $row, bool $isAr, bool $includeDetail = false): array
    {
        $base = match ($tab) {
            'cities' => [
                'id' => $row->id,
                'number' => $row->number,
                'slug' => is_array($row->detail_ar) ? ($row->detail_ar['slug'] ?? null) : null,
                'name' => $isAr ? $row->name_ar : $row->name_en,
                'description' => $isAr ? $row->description_ar : $row->description_en,
                'countryCode' => $row->country_code,
                'citySize' => $row->city_size,
            ],
            'projects' => [
                'id' => $row->id,
                'number' => $row->number,
                'slug' => is_array($row->detail_ar) ? ($row->detail_ar['slug'] ?? null) : null,
                'city' => $isAr ? $row->city_ar : $row->city_en,
                'country' => $isAr ? $row->country_ar : $row->country_en,
                'startDate' => $row->start_date,
                'endDate' => $row->end_date,
                'title' => trim(($isAr ? $row->city_ar : $row->city_en).', '.($isAr ? $row->country_ar : $row->country_en)),
                ...$this->projectProfileFields($row, $isAr),
            ],
            'organizations' => [
                'id' => $row->id,
                'number' => $row->number,
                'name' => $isAr ? $row->name_ar : $row->name_en,
                'description' => $isAr ? $row->description_ar : $row->description_en,
                ...$this->organizationProfileFields($row, $isAr),
            ],
            'publications' => [
                'id' => $row->id,
                'number' => $row->number,
                'name' => $isAr ? $row->name_ar : $row->name_en,
                'description' => $isAr ? $row->description_ar : $row->description_en,
                ...$this->publicationProfileFields($row, $isAr),
            ],
        };

        if ($includeDetail) {
            $detail = $isAr ? ($row->detail_ar ?? []) : ($row->detail_en ?? []);
            $detail = is_array($detail) ? $detail : [];
            $base['detail'] = ImageUrl::mapBodyPaths($detail) ?? $detail;
        }

        return $base;
    }

    /**
     * @return array<string, mixed>
     */
    private function organizationProfileFields(Model $row, bool $isAr): array
    {
        $detail = $isAr ? ($row->detail_ar ?? []) : ($row->detail_en ?? []);
        if (! is_array($detail)) {
            return [];
        }

        $keys = [
            'type',
            'country',
            'countryCode',
            'address',
            'phone',
            'email',
            'website',
            'founded',
            'employees',
            'budget',
            'interventionAreas',
            'interventionFields',
            'interventionTypes',
            'socialLinks',
        ];

        return array_intersect_key($detail, array_flip($keys));
    }

    /**
     * @return array<string, mixed>
     */
    private function projectProfileFields(Model $row, bool $isAr): array
    {
        $detail = $isAr ? ($row->detail_ar ?? []) : ($row->detail_en ?? []);
        if (! is_array($detail)) {
            return [];
        }

        $keys = [
            'slug',
            'layout',
            'heroImage',
            'mapImage',
            'valuesContent',
            'policyToolsContent',
            'sources',
            'founders',
            'references',
            'relatedProjects',
        ];

        return array_intersect_key($detail, array_flip($keys));
    }

    /**
     * @return array<string, mixed>
     */
    private function publicationProfileFields(Model $row, bool $isAr): array
    {
        $detail = $isAr ? ($row->detail_ar ?? []) : ($row->detail_en ?? []);
        if (! is_array($detail)) {
            return [];
        }

        $keys = [
            'organizationName',
            'organizationType',
            'publicationCountry',
            'languages',
            'publicationDate',
            'publicationType',
            'topics',
            'publicationLink',
            'coverImage',
            'languageVersions',
        ];

        return array_intersect_key($detail, array_flip($keys));
    }
}

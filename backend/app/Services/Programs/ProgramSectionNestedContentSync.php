<?php

declare(strict_types=1);

namespace App\Services\Programs;

use App\Models\DirectoryCity;
use App\Models\DirectoryDiscussion;
use App\Models\DirectoryOrganization;
use App\Models\DirectoryProject;
use App\Models\DirectoryPublication;
use App\Models\Expert;
use App\Models\ProgramSection;
use App\Models\TrainingCourse;
use App\Support\ImageUrl;

/**
 * When program-section-details body includes courses[] or experts[],
 * sync rows to their dedicated tables (used by the public programs API).
 */
class ProgramSectionNestedContentSync
{
    public function syncFromDetail(ProgramSection $section, ?array $bodyAr, ?array $bodyEn): void
    {
        match ($section->tab_key) {
            'trainingPrograms' => $this->syncTrainingCourses($bodyAr ?? [], $bodyEn ?? []),
            'experts' => $this->syncExperts($bodyAr ?? [], $bodyEn ?? []),
            'developmentPortal' => $this->syncDevelopmentPortalDirectory($bodyAr ?? [], $bodyEn ?? []),
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $bodyAr
     * @param  array<string, mixed>  $bodyEn
     */
    private function syncTrainingCourses(array $bodyAr, array $bodyEn): void
    {
        $arCourses = $bodyAr['courses'] ?? [];
        if (! is_array($arCourses) || $arCourses === []) {
            return;
        }

        $enCourses = is_array($bodyEn['courses'] ?? null) ? $bodyEn['courses'] : [];

        foreach ($arCourses as $index => $course) {
            if (! is_array($course)) {
                continue;
            }

            $enCourse = is_array($enCourses[$index] ?? null) ? $enCourses[$index] : $course;

            TrainingCourse::query()->updateOrCreate(
                ['sort_order' => $index],
                [
                    'title_ar' => (string) ($course['title'] ?? ''),
                    'title_en' => (string) ($enCourse['title'] ?? ($course['title'] ?? '')),
                    'count_ar' => (string) ($course['count'] ?? ''),
                    'count_en' => (string) ($enCourse['count'] ?? ($course['count'] ?? '')),
                ],
            );
        }

        TrainingCourse::query()->where('sort_order', '>=', count($arCourses))->delete();
    }

    /**
     * @param  array<string, mixed>  $bodyAr
     * @param  array<string, mixed>  $bodyEn
     */
    private function syncExperts(array $bodyAr, array $bodyEn): void
    {
        $arExperts = $bodyAr['experts'] ?? [];
        if (! is_array($arExperts) || $arExperts === []) {
            return;
        }

        $enExperts = is_array($bodyEn['experts'] ?? null) ? $bodyEn['experts'] : [];
        $enByName = collect($enExperts)
            ->filter(fn ($row) => is_array($row))
            ->keyBy(fn (array $row) => (string) ($row['name'] ?? ''));

        foreach ($arExperts as $index => $expert) {
            if (! is_array($expert)) {
                continue;
            }

            $name = (string) ($expert['name'] ?? '');
            $enExpert = $enByName->get($name, $expert);
            if (! is_array($enExpert)) {
                $enExpert = $expert;
            }

            Expert::query()->updateOrCreate(
                ['sort_order' => $index],
                [
                    'name_ar' => $name,
                    'name_en' => (string) ($enExpert['name'] ?? $name),
                    'specialty_ar' => (string) ($expert['specialty'] ?? ''),
                    'specialty_en' => (string) ($enExpert['specialty'] ?? ($expert['specialty'] ?? '')),
                    'image_url' => ImageUrl::normalizeStoredPath($expert['image'] ?? null),
                ],
            );
        }

        Expert::query()->where('sort_order', '>=', count($arExperts))->delete();
    }

    /**
     * @param  array<string, mixed>  $bodyAr
     * @param  array<string, mixed>  $bodyEn
     */
    private function syncDevelopmentPortalDirectory(array $bodyAr, array $bodyEn): void
    {
        $arRows = $bodyAr['directory']['rows'] ?? null;
        if (! is_array($arRows) || $arRows === []) {
            return;
        }

        $enRows = is_array($bodyEn['directory']['rows'] ?? null) ? $bodyEn['directory']['rows'] : [];

        $this->syncDirectoryCities($arRows['cities'] ?? [], $enRows['cities'] ?? []);
        $this->syncDirectoryProjects($arRows['projects'] ?? [], $enRows['projects'] ?? []);
        $this->syncDirectoryOrganizations($arRows['organizations'] ?? [], $enRows['organizations'] ?? []);
        $this->syncDirectoryPublications($arRows['publications'] ?? [], $enRows['publications'] ?? []);
    }

    /**
     * @param  array<int, mixed>  $arCities
     * @param  array<int, mixed>  $enCities
     */
    private function syncDirectoryCities(array $arCities, array $enCities): void
    {
        if ($arCities === []) {
            return;
        }

        $numbers = [];

        foreach ($arCities as $index => $row) {
            if (! is_array($row)) {
                continue;
            }

            $number = (string) ($row['number'] ?? (string) ($index + 1));
            $numbers[] = $number;
            $enRow = is_array($enCities[$index] ?? null) ? $enCities[$index] : $row;
            $description = (string) ($row['description'] ?? '');

            DirectoryCity::query()->updateOrCreate(
                ['number' => $number],
                [
                    'name_ar' => (string) ($row['name'] ?? ''),
                    'name_en' => (string) ($enRow['name'] ?? ($row['name'] ?? '')),
                    'description_ar' => $description !== '' ? $description : null,
                    'description_en' => (string) ($enRow['description'] ?? $description) ?: null,
                    'country_code' => null,
                    'city_size' => $this->inferCitySize($description),
                    'detail_ar' => $this->normalizeDetail($this->cityDetailFromRow($row)),
                    'detail_en' => $this->normalizeDetail($this->cityDetailFromRow($enRow)),
                    'sort_order' => $index,
                ],
            );

            $this->syncDiscussions('cities', $number, $row['discussions'] ?? [], $enRow['discussions'] ?? []);
        }

        DirectoryCity::query()->whereNotIn('number', $numbers)->delete();
    }

    /**
     * @param  array<int, mixed>  $arProjects
     * @param  array<int, mixed>  $enProjects
     */
    private function syncDirectoryProjects(array $arProjects, array $enProjects): void
    {
        if ($arProjects === []) {
            return;
        }

        $numbers = [];

        foreach ($arProjects as $index => $row) {
            if (! is_array($row)) {
                continue;
            }

            $number = (string) ($row['number'] ?? (string) ($index + 1));
            $numbers[] = $number;
            $enRow = is_array($enProjects[$index] ?? null) ? $enProjects[$index] : $row;

            DirectoryProject::query()->updateOrCreate(
                ['number' => $number],
                [
                    'city_ar' => (string) ($row['city'] ?? ''),
                    'city_en' => (string) ($enRow['city'] ?? ($row['city'] ?? '')),
                    'country_ar' => (string) ($row['country'] ?? ''),
                    'country_en' => (string) ($enRow['country'] ?? ($row['country'] ?? '')),
                    'start_date' => $row['startDate'] ?? null,
                    'end_date' => $row['endDate'] ?? null,
                    'detail_ar' => $this->normalizeDetail($this->projectDetailFromRow($row)),
                    'detail_en' => $this->normalizeDetail($this->projectDetailFromRow($enRow)),
                    'sort_order' => $index,
                ],
            );

            $this->syncDiscussions('projects', $number, $row['discussions'] ?? [], $enRow['discussions'] ?? []);
        }

        DirectoryProject::query()->whereNotIn('number', $numbers)->delete();
    }

    /**
     * @param  array<int, mixed>  $arRows
     * @param  array<int, mixed>  $enRows
     */
    private function syncDirectoryOrganizations(array $arRows, array $enRows): void
    {
        if ($arRows === []) {
            return;
        }

        $numbers = [];

        foreach ($arRows as $index => $row) {
            if (! is_array($row)) {
                continue;
            }

            $number = (string) ($row['number'] ?? (string) ($index + 1));
            $numbers[] = $number;
            $enRow = is_array($enRows[$index] ?? null) ? $enRows[$index] : $row;
            $description = (string) ($row['type'] ?? $row['description'] ?? '');

            DirectoryOrganization::query()->updateOrCreate(
                ['number' => $number],
                [
                    'name_ar' => (string) ($row['name'] ?? ''),
                    'name_en' => (string) ($enRow['name'] ?? ($row['name'] ?? '')),
                    'description_ar' => $description !== '' ? $description : null,
                    'description_en' => (string) ($enRow['type'] ?? $enRow['description'] ?? $description) ?: null,
                    'detail_ar' => $this->normalizeDetail($this->organizationDetailFromRow($row)),
                    'detail_en' => $this->normalizeDetail($this->organizationDetailFromRow($enRow)),
                    'sort_order' => $index,
                ],
            );

            $this->syncDiscussions('organizations', $number, $row['discussions'] ?? [], $enRow['discussions'] ?? []);
        }

        DirectoryOrganization::query()->whereNotIn('number', $numbers)->delete();
    }

    /**
     * @param  array<int, mixed>  $arRows
     * @param  array<int, mixed>  $enRows
     */
    private function syncDirectoryPublications(array $arRows, array $enRows): void
    {
        if ($arRows === []) {
            return;
        }

        $numbers = [];

        foreach ($arRows as $index => $row) {
            if (! is_array($row)) {
                continue;
            }

            $number = (string) ($row['number'] ?? (string) ($index + 1));
            $numbers[] = $number;
            $enRow = is_array($enRows[$index] ?? null) ? $enRows[$index] : $row;
            $description = (string) ($row['description'] ?? '');

            DirectoryPublication::query()->updateOrCreate(
                ['number' => $number],
                [
                    'name_ar' => (string) ($row['name'] ?? ''),
                    'name_en' => (string) ($enRow['name'] ?? ($row['name'] ?? '')),
                    'description_ar' => $description !== '' ? $description : null,
                    'description_en' => (string) ($enRow['description'] ?? $description) ?: null,
                    'detail_ar' => $this->normalizeDetail($this->publicationDetailFromRow($row)),
                    'detail_en' => $this->normalizeDetail($this->publicationDetailFromRow($enRow)),
                    'sort_order' => $index,
                ],
            );

            $this->syncDiscussions('publications', $number, $row['discussions'] ?? [], $enRow['discussions'] ?? []);
        }

        DirectoryPublication::query()->whereNotIn('number', $numbers)->delete();
    }

    private function inferCitySize(string $description): ?string
    {
        if (str_contains($description, 'كبيرة') || str_contains($description, 'large')) {
            return 'large';
        }

        if (str_contains($description, 'متوسط') || str_contains($description, 'medium')) {
            return 'medium';
        }

        if (str_contains($description, 'صغيرة') || str_contains($description, 'small')) {
            return 'small';
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>|null
     */
    private function cityDetailFromRow(array $row): ?array
    {
        $detail = $row['detail'] ?? null;
        if (! is_array($detail)) {
            $detail = [];
        }

        if (isset($row['slug']) && is_string($row['slug'])) {
            $detail['slug'] = $row['slug'];
        }

        return $detail === [] ? null : $detail;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>|null
     */
    private function organizationDetailFromRow(array $row): ?array
    {
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

        $detail = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $row)) {
                $detail[$key] = $row[$key];
            }
        }

        if (isset($row['detail']) && is_array($row['detail'])) {
            $detail = array_merge($detail, $row['detail']);
        }

        return $detail === [] ? null : $detail;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>|null
     */
    private function projectDetailFromRow(array $row): ?array
    {
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

        $detail = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $row)) {
                $detail[$key] = $row[$key];
            }
        }

        if (isset($row['detail']) && is_array($row['detail'])) {
            $detail = array_merge($detail, $row['detail']);
        }

        return $detail === [] ? null : $detail;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>|null
     */
    private function publicationDetailFromRow(array $row): ?array
    {
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

        $detail = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $row)) {
                $detail[$key] = $row[$key];
            }
        }

        if (isset($row['detail']) && is_array($row['detail'])) {
            $detail = array_merge($detail, $row['detail']);
        }

        return $detail === [] ? null : $detail;
    }

    /**
     * @param  mixed  $detail
     * @return array<string, mixed>|null
     */
    private function normalizeDetail(mixed $detail): ?array
    {
        if (! is_array($detail) || $detail === []) {
            return null;
        }

        return ImageUrl::mapBodyPaths($detail) ?? $detail;
    }

    /**
     * @param  array<int, mixed>  $arDiscussions
     * @param  array<int, mixed>  $enDiscussions
     */
    private function syncDiscussions(string $type, string $number, array $arDiscussions, array $enDiscussions): void
    {
        if ($arDiscussions === []) {
            return;
        }

        DirectoryDiscussion::query()
            ->where('directory_type', $type)
            ->where('directory_number', $number)
            ->delete();

        foreach ($arDiscussions as $index => $discussion) {
            if (! is_array($discussion)) {
                continue;
            }

            $enDiscussion = is_array($enDiscussions[$index] ?? null) ? $enDiscussions[$index] : $discussion;
            $author = (string) ($discussion['author'] ?? '');
            $enAuthor = (string) ($enDiscussion['author'] ?? $author);

            DirectoryDiscussion::query()->create([
                'directory_type' => $type,
                'directory_number' => $number,
                'author_name_ar' => $author,
                'author_name_en' => $enAuthor,
                'body_ar' => (string) ($discussion['body'] ?? ''),
                'body_en' => (string) ($enDiscussion['body'] ?? ($discussion['body'] ?? '')),
                'is_approved' => true,
                'sort_order' => $index,
            ]);
        }
    }
}

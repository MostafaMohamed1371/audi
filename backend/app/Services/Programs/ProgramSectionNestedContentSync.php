<?php

declare(strict_types=1);

namespace App\Services\Programs;

use App\Models\DirectoryCity;
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
                    'sort_order' => $index,
                ],
            );
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
                    'sort_order' => $index,
                ],
            );
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
            $description = (string) ($row['description'] ?? '');

            DirectoryOrganization::query()->updateOrCreate(
                ['number' => $number],
                [
                    'name_ar' => (string) ($row['name'] ?? ''),
                    'name_en' => (string) ($enRow['name'] ?? ($row['name'] ?? '')),
                    'description_ar' => $description !== '' ? $description : null,
                    'description_en' => (string) ($enRow['description'] ?? $description) ?: null,
                    'sort_order' => $index,
                ],
            );
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
                    'sort_order' => $index,
                ],
            );
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
}

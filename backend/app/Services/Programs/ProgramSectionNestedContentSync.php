<?php

declare(strict_types=1);

namespace App\Services\Programs;

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
}

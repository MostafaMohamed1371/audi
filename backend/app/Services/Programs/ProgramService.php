<?php

declare(strict_types=1);

namespace App\Services\Programs;

use App\Models\AboutContent;
use App\Models\Expert;
use App\Models\Program;
use App\Models\ProgramSection;
use App\Models\TrainingCourse;
use App\Support\ImageUrl;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProgramService
{
    /**
     * @return array<string, mixed>
     */
    public function getProgram(string $slug, ?string $locale = null): array
    {
        $program = Program::query()->where('slug', $slug)->first();

        if (! $program) {
            throw new ModelNotFoundException("Program [{$slug}] not found.");
        }

        $isAr = ($locale ?? app()->getLocale()) === 'ar';
        $meta = $this->programMeta($slug, $isAr);

        $sections = $program->sections()->ordered()->get();
        $tabs = $sections->map(fn (ProgramSection $section) => [
            'id' => $section->tab_key,
            'label' => $isAr ? $section->title_ar : $section->title_en,
        ])->values()->all();

        $sectionPayload = [];

        foreach ($sections as $section) {
            $body = $isAr ? ($section->body_ar ?? []) : ($section->body_en ?? []);
            $body = is_array($body) ? $body : [];
            $body = ImageUrl::mapBodyPaths($body) ?? [];

            if ($section->tab_key === 'trainingPrograms') {
                $body['courses'] = $this->mapTrainingCourses($isAr);
            }

            if ($section->tab_key === 'experts') {
                $body['experts'] = $this->mapExperts($isAr);
            }

            if ($section->tab_key === 'developmentPortal' && isset($body['directory']) && is_array($body['directory'])) {
                unset($body['directory']['rows']);
            }

            $sectionPayload[$section->tab_key] = array_merge([
                'title' => $isAr ? $section->title_ar : $section->title_en,
                'intro' => $isAr ? $section->intro_ar : $section->intro_en,
                'image' => ImageUrl::public($section->image_url),
            ], $body);
        }

        return [
            'slug' => $program->slug,
            'title' => $isAr ? $program->title_ar : $program->title_en,
            'heroIntro' => $isAr ? $program->hero_intro_ar : $program->hero_intro_en,
            'back' => $meta['back'] ?? null,
            'sectionsLabel' => $meta['sectionsLabel'] ?? null,
            'tabs' => $tabs,
            'sections' => $sectionPayload,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function programMeta(string $slug, bool $isAr): array
    {
        $content = AboutContent::query()
            ->where('section_key', 'program_'.$slug)
            ->first();

        if (! $content) {
            return [];
        }

        $body = $isAr ? ($content->body_ar ?? []) : ($content->body_en ?? []);

        return is_array($body) ? $body : [];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function mapTrainingCourses(bool $isAr): array
    {
        return TrainingCourse::query()
            ->ordered()
            ->get()
            ->map(fn (TrainingCourse $course) => [
                'title' => $isAr ? $course->title_ar : $course->title_en,
                'count' => $isAr ? $course->count_ar : $course->count_en,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function mapExperts(bool $isAr): array
    {
        return Expert::query()
            ->ordered()
            ->get()
            ->map(fn (Expert $expert) => [
                'name' => $isAr ? $expert->name_ar : $expert->name_en,
                'specialty' => $isAr ? $expert->specialty_ar : $expert->specialty_en,
                'image' => ImageUrl::public($expert->image_url),
            ])
            ->values()
            ->all();
    }
}

<?php

declare(strict_types=1);

namespace App\Support;

final class AboutContentBodyRules
{
    /**
     * Validation rules for bodyAr/bodyEn based on section_key.
     *
     * @return array<string, mixed>
     */
    public static function rules(string $sectionKey, bool $partial = false): array
    {
        if (str_starts_with($sectionKey, 'program_')) {
            $parsed = ProgramContentKey::parse($sectionKey);

            if ($parsed !== null && $parsed['type'] === 'section') {
                return [];
            }

            return self::programMetaRules($partial);
        }

        return match ($sectionKey) {
            'institute' => self::instituteRules($partial),
            'tasks' => self::taskItemRules($partial),
            'goals' => self::taskItemRules($partial),
            'values' => self::valuesRules($partial),
            'vision_mission' => self::visionMissionRules($partial),
            'structure' => self::structureRules($partial),
            'partners_hero' => self::partnersHeroRules($partial),
            'advisory_board', 'team' => self::readMoreRules($partial),
            'focus_areas_pages' => self::focusAreasPagesRules($partial),
            'home_stats' => self::homeStatsRules($partial),
            'home_about_intro' => self::homeAboutIntroRules($partial),
            'home_programs' => self::homeProgramsRules($partial),
            'home_media_center' => self::homeMediaCenterRules($partial),
            'home_knowledge_center' => self::homeKnowledgeCenterRules($partial),
            'home_membership_contact' => self::homeMembershipContactRules($partial),
            default => [],
        };
    }

    /**
     * @return array<string, mixed>
     */
    private static function programMetaRules(bool $partial): array
    {
        $r = $partial ? 'sometimes' : 'nullable';

        return [
            'bodyAr.back' => [$r, 'string', 'max:255'],
            'bodyAr.sectionsLabel' => [$r, 'string', 'max:255'],
            'bodyEn.back' => [$r, 'string', 'max:255'],
            'bodyEn.sectionsLabel' => [$r, 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function instituteRules(bool $partial): array
    {
        $r = $partial ? 'sometimes' : 'nullable';

        return [
            'bodyAr.paragraphs' => [$r, 'array'],
            'bodyAr.paragraphs.*' => ['string'],
            'bodyAr.headquartersTitle' => [$r, 'string', 'max:255'],
            'bodyEn.paragraphs' => [$r, 'array'],
            'bodyEn.paragraphs.*' => ['string'],
            'bodyEn.headquartersTitle' => [$r, 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function taskItemRules(bool $partial): array
    {
        $r = $partial ? 'sometimes' : 'nullable';

        return [
            'bodyAr.items' => [$r, 'array'],
            'bodyAr.items.*.description' => ['string'],
            'bodyEn.items' => [$r, 'array'],
            'bodyEn.items.*.description' => ['string'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function valuesRules(bool $partial): array
    {
        $r = $partial ? 'sometimes' : 'nullable';

        return [
            'bodyAr.items' => [$r, 'array'],
            'bodyAr.items.*.title' => ['string', 'max:255'],
            'bodyAr.items.*.description' => ['string'],
            'bodyEn.items' => [$r, 'array'],
            'bodyEn.items.*.title' => ['string', 'max:255'],
            'bodyEn.items.*.description' => ['string'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function visionMissionRules(bool $partial): array
    {
        $r = $partial ? 'sometimes' : 'nullable';
        $fields = ['visionTitle', 'visionText', 'missionTitle', 'missionText', 'readMore', 'visionImage', 'missionImage'];
        $rules = [];

        foreach ($fields as $field) {
            $rules["bodyAr.{$field}"] = [$r, 'string'];
            $rules["bodyEn.{$field}"] = [$r, 'string'];
        }

        return $rules;
    }

    /**
     * @return array<string, mixed>
     */
    private static function structureRules(bool $partial): array
    {
        $r = $partial ? 'sometimes' : 'nullable';

        return [
            'bodyAr.imageAlt' => [$r, 'string', 'max:255'],
            'bodyEn.imageAlt' => [$r, 'string', 'max:255'],
            'imageUrl' => [$r, 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function partnersHeroRules(bool $partial): array
    {
        $r = $partial ? 'sometimes' : 'nullable';

        return [
            'bodyAr.description' => [$r, 'string'],
            'bodyEn.description' => [$r, 'string'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function readMoreRules(bool $partial): array
    {
        $r = $partial ? 'sometimes' : 'nullable';

        return [
            'bodyAr.readMore' => [$r, 'string', 'max:255'],
            'bodyEn.readMore' => [$r, 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function focusAreasPagesRules(bool $partial): array
    {
        $r = $partial ? 'sometimes' : 'nullable';
        $fields = ['title', 'back', 'viewMore', 'previous', 'next'];
        $rules = [];

        foreach ($fields as $field) {
            $rules["bodyAr.{$field}"] = [$r, 'string', 'max:255'];
            $rules["bodyEn.{$field}"] = [$r, 'string', 'max:255'];
        }

        return $rules;
    }

    /**
     * @return array<string, mixed>
     */
    private static function homeStatsRules(bool $partial): array
    {
        $r = $partial ? 'sometimes' : 'nullable';

        return [
            'bodyAr.subtitle' => [$r, 'string', 'max:500'],
            'bodyEn.subtitle' => [$r, 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function homeAboutIntroRules(bool $partial): array
    {
        $r = $partial ? 'sometimes' : 'nullable';
        $block = ['title', 'description', 'readMore'];

        $rules = [
            'bodyAr.description' => [$r, 'string'],
            'bodyAr.cta' => [$r, 'string', 'max:255'],
            'bodyEn.description' => [$r, 'string'],
            'bodyEn.cta' => [$r, 'string', 'max:255'],
        ];

        foreach (['mission', 'vision'] as $section) {
            foreach ($block as $field) {
                $rules["bodyAr.{$section}.{$field}"] = [$r, 'string'];
                $rules["bodyEn.{$section}.{$field}"] = [$r, 'string'];
            }
        }

        return $rules;
    }

    /**
     * @return array<string, mixed>
     */
    private static function homeProgramsRules(bool $partial): array
    {
        $r = $partial ? 'sometimes' : 'nullable';

        return [
            'bodyAr.cta' => [$r, 'string', 'max:255'],
            'bodyEn.cta' => [$r, 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function homeMediaCenterRules(bool $partial): array
    {
        $r = $partial ? 'sometimes' : 'nullable';

        return [
            'bodyAr.subtitle' => [$r, 'string'],
            'bodyAr.readMore' => [$r, 'string', 'max:255'],
            'bodyAr.viewAll' => [$r, 'string', 'max:255'],
            'bodyEn.subtitle' => [$r, 'string'],
            'bodyEn.readMore' => [$r, 'string', 'max:255'],
            'bodyEn.viewAll' => [$r, 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function homeKnowledgeCenterRules(bool $partial): array
    {
        $r = $partial ? 'sometimes' : 'nullable';

        return [
            'bodyAr.viewIssue' => [$r, 'string', 'max:255'],
            'bodyAr.downloadPdf' => [$r, 'string', 'max:255'],
            'bodyAr.headerSlides' => [$r, 'array'],
            'bodyAr.headerSlides.*.title' => ['string', 'max:500'],
            'bodyAr.headerSlides.*.description' => ['string'],
            'bodyEn.viewIssue' => [$r, 'string', 'max:255'],
            'bodyEn.downloadPdf' => [$r, 'string', 'max:255'],
            'bodyEn.headerSlides' => [$r, 'array'],
            'bodyEn.headerSlides.*.title' => ['string', 'max:500'],
            'bodyEn.headerSlides.*.description' => ['string'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function homeMembershipContactRules(bool $partial): array
    {
        $r = $partial ? 'sometimes' : 'nullable';

        return [
            'bodyAr.membership.title' => [$r, 'string', 'max:255'],
            'bodyAr.membership.subtitle' => [$r, 'string'],
            'bodyAr.membership.cta' => [$r, 'string', 'max:255'],
            'bodyAr.membership.href' => [$r, 'string', 'max:500'],
            'bodyAr.contact.title' => [$r, 'string', 'max:255'],
            'bodyAr.contact.addressTitle' => [$r, 'string', 'max:255'],
            'bodyEn.membership.title' => [$r, 'string', 'max:255'],
            'bodyEn.membership.subtitle' => [$r, 'string'],
            'bodyEn.membership.cta' => [$r, 'string', 'max:255'],
            'bodyEn.membership.href' => [$r, 'string', 'max:500'],
            'bodyEn.contact.title' => [$r, 'string', 'max:255'],
            'bodyEn.contact.addressTitle' => [$r, 'string', 'max:255'],
        ];
    }
}

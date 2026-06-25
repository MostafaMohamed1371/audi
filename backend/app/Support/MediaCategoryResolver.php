<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\MediaCategory;
use InvalidArgumentException;

class MediaCategoryResolver
{
    /**
     * @return array<string, MediaCategory>
     */
    public static function routeMap(): array
    {
        return [
            'news' => MediaCategory::News,
            'newsletter' => MediaCategory::Newsletter,
            'city-meetings' => MediaCategory::CityMeetings,
            'city_meetings' => MediaCategory::CityMeetings,
            'secretary-speaks' => MediaCategory::SecretarySpeaks,
            'secretary_speaks' => MediaCategory::SecretarySpeaks,
        ];
    }

    public static function resolve(string $category): MediaCategory
    {
        $map = self::routeMap();

        if (! isset($map[$category])) {
            throw new InvalidArgumentException("Unknown media category: {$category}");
        }

        return $map[$category];
    }

    public static function toRoute(MediaCategory $category): string
    {
        return match ($category) {
            MediaCategory::News => 'news',
            MediaCategory::Newsletter => 'newsletter',
            MediaCategory::CityMeetings => 'city-meetings',
            MediaCategory::SecretarySpeaks => 'secretary-speaks',
        };
    }

    public static function toFrontend(MediaCategory $category): string
    {
        return match ($category) {
            MediaCategory::News => 'news',
            MediaCategory::Newsletter => 'newsletter',
            MediaCategory::CityMeetings => 'cityMeetings',
            MediaCategory::SecretarySpeaks => 'secretarySpeaks',
        };
    }
}

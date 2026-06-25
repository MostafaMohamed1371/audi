<?php

declare(strict_types=1);

namespace App\Support;

use Carbon\CarbonInterface;

class PublishedDateFormatter
{
    public static function format(?CarbonInterface $date, ?string $locale = null): ?string
    {
        if ($date === null) {
            return null;
        }

        $locale = $locale ?? app()->getLocale();

        if ($locale === 'ar') {
            return $date->format('d/m/Y');
        }

        return strtoupper($date->format('M d, Y'));
    }
}

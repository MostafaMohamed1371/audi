<?php

declare(strict_types=1);

namespace App\Models\Concerns;

trait LocalizesAttributes
{
    public function localized(string $attribute, ?string $locale = null): mixed
    {
        $locale = $locale ?? app()->getLocale();
        $suffix = $locale === 'en' ? '_en' : '_ar';

        return $this->getAttribute($attribute.$suffix);
    }
}

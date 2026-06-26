<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Resource;
use App\Support\ImageUrl;
use App\Support\PublishedDateFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Resource */
class ResourceItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $request->attributes->get('locale', app()->getLocale());
        $isAr = $locale === 'ar';

        return [
            'slug' => $this->slug,
            'title' => $isAr ? $this->title_ar : $this->title_en,
            'date' => PublishedDateFormatter::format($this->published_date, $locale),
            'image' => ImageUrl::public($this->image_url),
            'downloadHref' => ImageUrl::public($this->file_url) ?? '#',
            'buttonVariant' => $this->buttonVariant(),
            'resourceType' => $this->resource_type,
            'year' => $this->year,
            'focusAreaSlug' => $this->focusArea?->slug,
        ];
    }

    private function buttonVariant(): string
    {
        return match ($this->sort_order % 4) {
            2 => 'secondary',
            3 => 'light',
            default => 'primary',
        };
    }
}

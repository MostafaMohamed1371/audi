<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Enums\MediaCategory;
use App\Models\MediaArticle;
use App\Support\MediaCategoryResolver;
use App\Support\ImageUrl;
use App\Support\PublishedDateFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin MediaArticle */
class MediaArticleDetailResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $request->attributes->get('locale', app()->getLocale());
        $isAr = $locale === 'ar';
        $category = MediaCategory::from($this->category);

        $payload = [
            'key' => $this->key,
            'slug' => $isAr ? $this->slug_ar : $this->slug_en,
            'slugAr' => $this->slug_ar,
            'slugEn' => $this->slug_en,
            'title' => $isAr ? $this->title_ar : $this->title_en,
            'date' => PublishedDateFormatter::format($this->published_date, $locale),
            'image' => ImageUrl::public($this->image_url),
            'body' => $isAr ? $this->body_ar : $this->body_en,
            'category' => MediaCategoryResolver::toFrontend($category),
        ];

        if ($category === MediaCategory::News) {
            $payload['description'] = $isAr ? $this->description_ar : $this->description_en;
        }

        if ($category === MediaCategory::Newsletter) {
            $payload['pdfHref'] = $this->pdf_url ?? '#';
        }

        if ($category === MediaCategory::CityMeetings) {
            $payload['authors'] = $isAr ? ($this->authors_ar ?? []) : ($this->authors_en ?? []);
            $payload['time'] = $this->event_time;
        }

        return $payload;
    }
}

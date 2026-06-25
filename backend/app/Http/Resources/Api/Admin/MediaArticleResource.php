<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\MediaArticle */
class MediaArticleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'key' => $this->key,
            'slugAr' => $this->slug_ar,
            'slugEn' => $this->slug_en,
            'titleAr' => $this->title_ar,
            'titleEn' => $this->title_en,
            'descriptionAr' => $this->description_ar,
            'descriptionEn' => $this->description_en,
            'bodyAr' => $this->body_ar,
            'bodyEn' => $this->body_en,
            'publishedDate' => $this->published_date?->format('Y-m-d'),
            'imageUrl' => $this->image_url,
            'pdfUrl' => $this->pdf_url,
            'authorsAr' => $this->authors_ar,
            'authorsEn' => $this->authors_en,
            'eventTime' => $this->event_time,
            'isPublished' => $this->is_published,
            'sortOrder' => $this->sort_order,
            'createdAt' => $this->created_at?->toIso8601String(),
            'updatedAt' => $this->updated_at?->toIso8601String(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Admin;

use App\Support\ImageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Resource */
class ResourceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'titleAr' => $this->title_ar,
            'titleEn' => $this->title_en,
            'publishedDate' => $this->published_date?->format('Y-m-d'),
            'imageUrl' => ImageUrl::api($this->image_url),
            'fileUrl' => ImageUrl::api($this->file_url),
            'resourceType' => $this->resource_type,
            'focusAreaId' => $this->focus_area_id,
            'year' => $this->year,
            'isPublished' => $this->is_published,
            'sortOrder' => $this->sort_order,
            'createdAt' => $this->created_at?->toIso8601String(),
            'updatedAt' => $this->updated_at?->toIso8601String(),
        ];
    }
}

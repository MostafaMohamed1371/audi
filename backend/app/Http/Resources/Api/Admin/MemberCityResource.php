<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Admin;

use App\Support\ImageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\MemberCity */
class MemberCityResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'countryCode' => $this->country_code,
            'nameAr' => $this->name_ar,
            'nameEn' => $this->name_en,
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
            'infoAr' => $this->info_ar,
            'infoEn' => $this->info_en,
            'imageUrl' => ImageUrl::api($this->image_url),
            'isActive' => $this->is_active,
            'createdAt' => $this->created_at?->toIso8601String(),
            'updatedAt' => $this->updated_at?->toIso8601String(),
        ];
    }
}

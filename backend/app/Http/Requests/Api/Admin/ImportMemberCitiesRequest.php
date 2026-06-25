<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ImportMemberCitiesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('upsertBy') && is_array($this->input('upsertBy'))) {
            $this->merge([
                'upsert_by' => array_map(function (string $field) {
                    return match ($field) {
                        'countryCode' => 'country_code',
                        'nameEn' => 'name_en',
                        'nameAr' => 'name_ar',
                        default => $field,
                    };
                }, $this->input('upsertBy')),
            ]);
        }

        if ($this->has('cities') && is_array($this->input('cities'))) {
            $cities = array_map(function (array $city) {
                return [
                    'country_code' => strtoupper((string) ($city['countryCode'] ?? $city['country_code'] ?? '')),
                    'name_ar' => $city['nameAr'] ?? $city['name_ar'] ?? '',
                    'name_en' => $city['nameEn'] ?? $city['name_en'] ?? '',
                    'latitude' => $city['latitude'] ?? null,
                    'longitude' => $city['longitude'] ?? null,
                    'info_ar' => $city['infoAr'] ?? $city['info_ar'] ?? null,
                    'info_en' => $city['infoEn'] ?? $city['info_en'] ?? null,
                    'image_url' => $city['imageUrl'] ?? $city['image_url'] ?? null,
                    'is_active' => $city['isActive'] ?? $city['is_active'] ?? true,
                ];
            }, $this->input('cities'));

            $this->merge(['cities' => $cities]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'cities' => ['required', 'array', 'min:1'],
            'cities.*.country_code' => ['required', 'string', 'size:2'],
            'cities.*.name_ar' => ['required', 'string', 'max:255'],
            'cities.*.name_en' => ['required', 'string', 'max:255'],
            'cities.*.latitude' => ['required', 'numeric', 'between:-90,90'],
            'cities.*.longitude' => ['required', 'numeric', 'between:-180,180'],
            'cities.*.info_ar' => ['nullable', 'string'],
            'cities.*.info_en' => ['nullable', 'string'],
            'cities.*.image_url' => ['nullable', 'url'],
            'cities.*.is_active' => ['boolean'],
            'upsert_by' => ['sometimes', 'array'],
            'upsert_by.*' => ['string', 'in:country_code,name_en,name_ar'],
        ];
    }
}

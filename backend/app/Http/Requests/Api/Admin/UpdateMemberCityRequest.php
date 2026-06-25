<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->has('countryCode')) {
            $merge['country_code'] = strtoupper((string) $this->input('countryCode'));
        }
        if ($this->has('nameAr')) {
            $merge['name_ar'] = $this->input('nameAr');
        }
        if ($this->has('nameEn')) {
            $merge['name_en'] = $this->input('nameEn');
        }
        if ($this->has('infoAr')) {
            $merge['info_ar'] = $this->input('infoAr');
        }
        if ($this->has('infoEn')) {
            $merge['info_en'] = $this->input('infoEn');
        }
        if ($this->has('imageUrl')) {
            $merge['image_url'] = $this->input('imageUrl');
        }
        if ($this->has('isActive')) {
            $merge['is_active'] = $this->input('isActive');
        }

        $this->merge($merge);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'country_code' => ['sometimes', 'string', 'size:2', 'exists:countries,code_a2'],
            'name_ar' => ['sometimes', 'string', 'max:255'],
            'name_en' => ['sometimes', 'string', 'max:255'],
            'latitude' => ['sometimes', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'numeric', 'between:-180,180'],
            'info_ar' => ['nullable', 'string', 'max:5000'],
            'info_en' => ['nullable', 'string', 'max:5000'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'is_active' => ['boolean'],
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'country_code' => strtoupper((string) $this->input('country_code', $this->input('countryCode', ''))),
            'name_ar' => $this->input('name_ar', $this->input('nameAr')),
            'name_en' => $this->input('name_en', $this->input('nameEn')),
            'info_ar' => $this->input('info_ar', $this->input('infoAr')),
            'info_en' => $this->input('info_en', $this->input('infoEn')),
            'image_url' => $this->input('image_url', $this->input('imageUrl')),
            'is_active' => $this->input('is_active', $this->input('isActive', true)),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'country_code' => ['required', 'string', 'size:2', 'exists:countries,code_a2'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'info_ar' => ['nullable', 'string', 'max:5000'],
            'info_en' => ['nullable', 'string', 'max:5000'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'is_active' => ['boolean'],
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'titleAr' => ['sometimes', 'nullable', 'string', 'max:255'],
            'titleEn' => ['sometimes', 'nullable', 'string', 'max:255'],
            'subtitleAr' => ['sometimes', 'nullable', 'string'],
            'subtitleEn' => ['sometimes', 'nullable', 'string'],
            'addressLabelAr' => ['sometimes', 'nullable', 'string', 'max:255'],
            'addressLabelEn' => ['sometimes', 'nullable', 'string', 'max:255'],
            'addressAr' => ['sometimes', 'nullable', 'string'],
            'addressEn' => ['sometimes', 'nullable', 'string'],
            'mapTitleAr' => ['sometimes', 'nullable', 'string', 'max:255'],
            'mapTitleEn' => ['sometimes', 'nullable', 'string', 'max:255'],
            'mapEmbedUrlAr' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'mapEmbedUrlEn' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'itemsAr' => ['sometimes', 'nullable', 'array'],
            'itemsAr.*.label' => ['required_with:itemsAr', 'string', 'max:255'],
            'itemsAr.*.value' => ['required_with:itemsAr', 'string', 'max:255'],
            'itemsAr.*.type' => ['required_with:itemsAr', 'string', 'max:60'],
            'itemsAr.*.href' => ['nullable', 'string', 'max:500'],
            'itemsEn' => ['sometimes', 'nullable', 'array'],
            'itemsEn.*.label' => ['required_with:itemsEn', 'string', 'max:255'],
            'itemsEn.*.value' => ['required_with:itemsEn', 'string', 'max:255'],
            'itemsEn.*.type' => ['required_with:itemsEn', 'string', 'max:60'],
            'itemsEn.*.href' => ['nullable', 'string', 'max:500'],
        ];
    }
}

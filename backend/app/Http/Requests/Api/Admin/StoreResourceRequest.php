<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResourceRequest extends FormRequest
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
            'slug' => ['required', 'string', 'max:120', Rule::unique('resources', 'slug')],
            'titleAr' => ['required', 'string', 'max:500'],
            'titleEn' => ['required', 'string', 'max:500'],
            'publishedDate' => ['nullable', 'date'],
            'imageUrl' => ['nullable', 'string', 'max:500'],
            'fileUrl' => ['nullable', 'string', 'max:500'],
            'resourceType' => ['nullable', 'string', 'max:80'],
            'focusAreaId' => ['nullable', 'integer', Rule::exists('focus_areas', 'id')],
            'knowledgeCategoryId' => ['nullable', 'integer', Rule::exists('knowledge_categories', 'id')],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'isPublished' => ['sometimes', 'boolean'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validatedPayload(): array
    {
        $data = $this->validated();

        return [
            'slug' => $data['slug'],
            'title_ar' => $data['titleAr'],
            'title_en' => $data['titleEn'],
            'published_date' => $data['publishedDate'] ?? null,
            'image_url' => $data['imageUrl'] ?? null,
            'file_url' => $data['fileUrl'] ?? null,
            'resource_type' => $data['resourceType'] ?? null,
            'focus_area_id' => $data['focusAreaId'] ?? null,
            'knowledge_category_id' => $data['knowledgeCategoryId'] ?? null,
            'year' => $data['year'] ?? null,
            'is_published' => $data['isPublished'] ?? true,
            'sort_order' => $data['sortOrder'] ?? 0,
        ];
    }
}

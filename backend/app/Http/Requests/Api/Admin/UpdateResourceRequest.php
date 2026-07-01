<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin;

use Illuminate\Validation\Rule;

class UpdateResourceRequest extends StoreResourceRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $resourceId = $this->route('resource')?->id ?? $this->route('id');

        return [
            'slug' => ['sometimes', 'string', 'max:120', Rule::unique('resources', 'slug')->ignore($resourceId)],
            'titleAr' => ['sometimes', 'string', 'max:500'],
            'titleEn' => ['sometimes', 'string', 'max:500'],
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
        $map = [
            'slug' => 'slug',
            'titleAr' => 'title_ar',
            'titleEn' => 'title_en',
            'publishedDate' => 'published_date',
            'imageUrl' => 'image_url',
            'fileUrl' => 'file_url',
            'resourceType' => 'resource_type',
            'focusAreaId' => 'focus_area_id',
            'knowledgeCategoryId' => 'knowledge_category_id',
            'year' => 'year',
            'isPublished' => 'is_published',
            'sortOrder' => 'sort_order',
        ];

        $payload = [];

        foreach ($map as $input => $column) {
            if ($this->has($input)) {
                $payload[$column] = $this->input($input);
            }
        }

        return $payload;
    }
}

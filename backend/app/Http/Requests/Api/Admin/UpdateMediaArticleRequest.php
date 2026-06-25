<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin;

use Illuminate\Validation\Rule;

class UpdateMediaArticleRequest extends StoreMediaArticleRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $articleId = $this->route('mediaArticle')?->id ?? $this->route('id');

        return [
            'category' => ['sometimes', Rule::enum(\App\Enums\MediaCategory::class)],
            'key' => ['sometimes', 'string', 'max:120', Rule::unique('media_articles', 'key')->ignore($articleId)],
            'slugAr' => ['sometimes', 'string', 'max:255'],
            'slugEn' => ['sometimes', 'string', 'max:255'],
            'titleAr' => ['sometimes', 'string', 'max:500'],
            'titleEn' => ['sometimes', 'string', 'max:500'],
            'descriptionAr' => ['nullable', 'string'],
            'descriptionEn' => ['nullable', 'string'],
            'bodyAr' => ['sometimes', 'array', 'min:1'],
            'bodyAr.*' => ['string'],
            'bodyEn' => ['sometimes', 'array', 'min:1'],
            'bodyEn.*' => ['string'],
            'publishedDate' => ['nullable', 'date'],
            'imageUrl' => ['nullable', 'string', 'max:500'],
            'pdfUrl' => ['nullable', 'string', 'max:500'],
            'authorsAr' => ['nullable', 'array'],
            'authorsAr.*' => ['string'],
            'authorsEn' => ['nullable', 'array'],
            'authorsEn.*' => ['string'],
            'eventTime' => ['nullable', 'string', 'max:120'],
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
            'category' => 'category',
            'key' => 'key',
            'slugAr' => 'slug_ar',
            'slugEn' => 'slug_en',
            'titleAr' => 'title_ar',
            'titleEn' => 'title_en',
            'descriptionAr' => 'description_ar',
            'descriptionEn' => 'description_en',
            'bodyAr' => 'body_ar',
            'bodyEn' => 'body_en',
            'publishedDate' => 'published_date',
            'imageUrl' => 'image_url',
            'pdfUrl' => 'pdf_url',
            'authorsAr' => 'authors_ar',
            'authorsEn' => 'authors_en',
            'eventTime' => 'event_time',
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

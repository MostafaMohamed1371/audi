<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin;

use App\Enums\MediaCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMediaArticleRequest extends FormRequest
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
        return $this->articleRules();
    }

    /**
     * @return array<string, mixed>
     */
    protected function articleRules(bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';

        return [
            'category' => [$required, Rule::enum(MediaCategory::class)],
            'key' => [$required, 'string', 'max:120', Rule::unique('media_articles', 'key')],
            'slugAr' => [$required, 'string', 'max:255'],
            'slugEn' => [$required, 'string', 'max:255'],
            'titleAr' => [$required, 'string', 'max:500'],
            'titleEn' => [$required, 'string', 'max:500'],
            'descriptionAr' => ['nullable', 'string'],
            'descriptionEn' => ['nullable', 'string'],
            'bodyAr' => [$required, 'array', 'min:1'],
            'bodyAr.*' => ['string'],
            'bodyEn' => [$required, 'array', 'min:1'],
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
        $data = $this->validated();

        return [
            'category' => $data['category'],
            'key' => $data['key'],
            'slug_ar' => $data['slugAr'],
            'slug_en' => $data['slugEn'],
            'title_ar' => $data['titleAr'],
            'title_en' => $data['titleEn'],
            'description_ar' => $data['descriptionAr'] ?? null,
            'description_en' => $data['descriptionEn'] ?? null,
            'body_ar' => $data['bodyAr'],
            'body_en' => $data['bodyEn'],
            'published_date' => $data['publishedDate'] ?? null,
            'image_url' => $data['imageUrl'] ?? null,
            'pdf_url' => $data['pdfUrl'] ?? null,
            'authors_ar' => $data['authorsAr'] ?? null,
            'authors_en' => $data['authorsEn'] ?? null,
            'event_time' => $data['eventTime'] ?? null,
            'is_published' => $data['isPublished'] ?? true,
            'sort_order' => $data['sortOrder'] ?? 0,
        ];
    }
}

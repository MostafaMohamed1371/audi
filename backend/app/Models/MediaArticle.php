<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasSortOrder;
use App\Models\Concerns\LocalizesAttributes;

class MediaArticle extends Model
{
    use HasFactory, HasSortOrder, LocalizesAttributes;
    protected $fillable = [
  'category',
  'key',
  'slug_ar',
  'slug_en',
  'title_ar',
  'title_en',
  'description_ar',
  'description_en',
  'body_ar',
  'body_en',
  'published_date',
  'image_url',
  'pdf_url',
  'authors_ar',
  'authors_en',
  'event_time',
  'is_published',
  'sort_order',
];

    protected function casts(): array
    {
        return [
  'body_ar' => 'array',
  'body_en' => 'array',
  'authors_ar' => 'array',
  'authors_en' => 'array',
  'published_date' => 'date',
  'is_published' => 'boolean',
];
    }
}

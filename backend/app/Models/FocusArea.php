<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\HasSortOrder;
use App\Models\Concerns\LocalizesAttributes;

class FocusArea extends Model
{
    use HasFactory, HasSortOrder, LocalizesAttributes;
    protected $fillable = [
  'slug',
  'number',
  'title_ar',
  'title_en',
  'highlight_ar',
  'highlight_en',
  'tags_ar',
  'tags_en',
  'description_ar',
  'description_en',
  'list_image_url',
  'detail_image_url',
  'is_published',
  'sort_order',
];

    protected function casts(): array
    {
        return [
  'tags_ar' => 'array',
  'tags_en' => 'array',
  'is_published' => 'boolean',
];
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }
}

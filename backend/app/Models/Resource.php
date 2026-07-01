<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\HasSortOrder;
use App\Models\Concerns\LocalizesAttributes;

class Resource extends Model
{
    use HasFactory, HasSortOrder, LocalizesAttributes;
    protected $fillable = [
  'slug',
  'title_ar',
  'title_en',
  'published_date',
  'image_url',
  'file_url',
  'resource_type',
  'focus_area_id',
  'knowledge_category_id',
  'year',
  'is_published',
  'sort_order',
];

    protected function casts(): array
    {
        return [
  'published_date' => 'date',
  'is_published' => 'boolean',
  'year' => 'integer',
];
    }

    public function focusArea(): BelongsTo
    {
        return $this->belongsTo(FocusArea::class, 'focus_area_id');
    }

    public function knowledgeCategory(): BelongsTo
    {
        return $this->belongsTo(KnowledgeCategory::class, 'knowledge_category_id');
    }
}

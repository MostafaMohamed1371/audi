<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasSortOrder;
use App\Models\Concerns\LocalizesAttributes;

class StrategyDiagramItem extends Model
{
    use HasFactory, HasSortOrder, LocalizesAttributes;
    protected $fillable = [
  'item_key',
  'title_ar',
  'title_en',
  'content_ar',
  'content_en',
  'columns_ar',
  'columns_en',
  'sort_order',
];

    protected function casts(): array
    {
        return [
  'columns_ar' => 'array',
  'columns_en' => 'array',
];
    }
}

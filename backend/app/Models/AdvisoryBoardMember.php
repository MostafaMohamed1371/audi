<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasSortOrder;
use App\Models\Concerns\LocalizesAttributes;

class AdvisoryBoardMember extends Model
{
    use HasFactory, HasSortOrder, LocalizesAttributes;
    protected $fillable = [
  'name_ar',
  'name_en',
  'role_ar',
  'role_en',
  'bio_ar',
  'bio_en',
  'image_url',
  'is_featured',
  'sort_order',
];

    protected function casts(): array
    {
        return [
  'is_featured' => 'boolean',
];
    }
}

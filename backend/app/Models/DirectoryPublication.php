<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasSortOrder;
use App\Models\Concerns\LocalizesAttributes;

class DirectoryPublication extends Model
{
    use HasFactory, HasSortOrder, LocalizesAttributes;
    protected $table = 'directory_publications';

    protected $fillable = [
  'number',
  'name_ar',
  'name_en',
  'description_ar',
  'description_en',
  'detail_ar',
  'detail_en',
  'sort_order',
];

    protected function casts(): array
    {
        return [
            'detail_ar' => 'array',
            'detail_en' => 'array',
        ];
    }
}

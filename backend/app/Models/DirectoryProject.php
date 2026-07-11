<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasSortOrder;
use App\Models\Concerns\LocalizesAttributes;

class DirectoryProject extends Model
{
    use HasFactory, HasSortOrder, LocalizesAttributes;
    protected $table = 'directory_projects';

    protected $fillable = [
  'number',
  'city_ar',
  'city_en',
  'country_ar',
  'country_en',
  'start_date',
  'end_date',
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

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasSortOrder;
use App\Models\Concerns\LocalizesAttributes;

class DirectoryCity extends Model
{
    use HasFactory, HasSortOrder, LocalizesAttributes;
    protected $table = 'directory_cities';

    protected $fillable = [
  'number',
  'name_ar',
  'name_en',
  'description_ar',
  'description_en',
  'country_code',
  'city_size',
  'sort_order',
];
}

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasSortOrder;
use App\Models\Concerns\LocalizesAttributes;

class HomeStat extends Model
{
    use HasFactory, HasSortOrder, LocalizesAttributes;
    protected $fillable = [
  'value',
  'label_ar',
  'label_en',
  'description_ar',
  'description_en',
  'sort_order',
];
}

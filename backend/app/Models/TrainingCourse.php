<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasSortOrder;
use App\Models\Concerns\LocalizesAttributes;

class TrainingCourse extends Model
{
    use HasFactory, HasSortOrder, LocalizesAttributes;
    protected $fillable = [
  'title_ar',
  'title_en',
  'count_ar',
  'count_en',
  'sort_order',
];
}

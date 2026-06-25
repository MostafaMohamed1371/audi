<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\LocalizesAttributes;

class StrategyPage extends Model
{
    use HasFactory, LocalizesAttributes;
    protected $fillable = [
  'slug',
  'booklet_title_ar',
  'booklet_title_en',
  'booklet_pdf_url',
  'intro_title_ar',
  'intro_title_en',
  'intro_subtitle_ar',
  'intro_subtitle_en',
];
}

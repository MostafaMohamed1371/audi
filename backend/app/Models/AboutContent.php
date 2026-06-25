<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\LocalizesAttributes;

class AboutContent extends Model
{
    use HasFactory, LocalizesAttributes;

    protected $table = 'about_content';

    protected $fillable = [
  'section_key',
  'title_ar',
  'title_en',
  'body_ar',
  'body_en',
  'image_url',
];

    protected function casts(): array
    {
        return [
  'body_ar' => 'array',
  'body_en' => 'array',
];
    }
}

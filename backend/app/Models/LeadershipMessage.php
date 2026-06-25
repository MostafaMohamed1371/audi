<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\LocalizesAttributes;

class LeadershipMessage extends Model
{
    use HasFactory, LocalizesAttributes;
    protected $fillable = [
  'type',
  'name_ar',
  'name_en',
  'position_ar',
  'position_en',
  'honorific_ar',
  'honorific_en',
  'quote_ar',
  'quote_en',
  'paragraphs_ar',
  'paragraphs_en',
  'image_url',
  'image_alt_ar',
  'image_alt_en',
];

    protected function casts(): array
    {
        return [
  'paragraphs_ar' => 'array',
  'paragraphs_en' => 'array',
];
    }
}

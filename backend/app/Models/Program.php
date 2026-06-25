<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\LocalizesAttributes;

class Program extends Model
{
    use HasFactory, LocalizesAttributes;
    protected $fillable = [
  'slug',
  'title_ar',
  'title_en',
  'hero_intro_ar',
  'hero_intro_en',
];

    public function sections(): HasMany
    {
        return $this->hasMany(ProgramSection::class)->ordered();
    }
}

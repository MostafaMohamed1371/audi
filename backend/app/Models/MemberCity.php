<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\LocalizesAttributes;

class MemberCity extends Model
{
    use HasFactory, LocalizesAttributes;
    protected $fillable = [
  'country_code',
  'name_ar',
  'name_en',
  'latitude',
  'longitude',
  'info_ar',
  'info_en',
  'image_url',
  'is_active',
];

    protected function casts(): array
    {
        return [
  'latitude' => 'decimal:6',
  'longitude' => 'decimal:6',
  'is_active' => 'boolean',
];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_code', 'code_a2');
    }
}

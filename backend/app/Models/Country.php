<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'code_a2';

    protected $fillable = [
  'code_a2',
  'code_a3',
  'name_en',
  'name_ar',
  'geojson',
];

    protected function casts(): array
    {
        return [
  'geojson' => 'array',
];
    }

    public function memberCitys(): HasMany
    {
        return $this->hasMany(MemberCity::class);
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberCityStat extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'key';

    protected $fillable = [
  'key',
  'value',
  'label_ar',
  'label_en',
  'unit_ar',
  'unit_en',
  'auto_calculate',
];

    protected function casts(): array
    {
        return [
  'auto_calculate' => 'boolean',
];
    }
}

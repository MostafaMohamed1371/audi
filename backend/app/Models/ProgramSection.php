<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\HasSortOrder;
use App\Models\Concerns\LocalizesAttributes;

class ProgramSection extends Model
{
    use HasFactory, HasSortOrder, LocalizesAttributes;
    protected $fillable = [
  'program_id',
  'tab_key',
  'title_ar',
  'title_en',
  'intro_ar',
  'intro_en',
  'body_ar',
  'body_en',
  'image_url',
  'sort_order',
];

    protected function casts(): array
    {
        return [
  'body_ar' => 'array',
  'body_en' => 'array',
];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
}

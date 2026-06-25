<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\HasSortOrder;
use App\Models\Concerns\LocalizesAttributes;

class Partner extends Model
{
    use HasFactory, HasSortOrder, LocalizesAttributes;
    protected $fillable = [
  'partner_category_id',
  'name_ar',
  'name_en',
  'logo_url',
  'is_featured',
  'sort_order',
];

    protected function casts(): array
    {
        return [
  'is_featured' => 'boolean',
];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PartnerCategory::class, 'partner_category_id');
    }
}

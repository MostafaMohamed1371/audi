<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\HasSortOrder;
use App\Models\Concerns\LocalizesAttributes;

class TeamMember extends Model
{
    use HasFactory, HasSortOrder, LocalizesAttributes;
    protected $fillable = [
  'team_section_id',
  'name_ar',
  'name_en',
  'role_ar',
  'role_en',
  'bio_ar',
  'bio_en',
  'image_url',
  'sort_order',
];

    public function section(): BelongsTo
    {
        return $this->belongsTo(TeamSection::class, 'team_section_id');
    }
}

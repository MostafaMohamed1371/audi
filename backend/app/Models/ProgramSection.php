<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'image_url',
        'sort_order',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function details(): HasOne
    {
        return $this->hasOne(ProgramSectionDetail::class, 'program_section_id');
    }

    public function aboutContent(): HasOne
    {
        return $this->hasOne(AboutContent::class, 'program_section_id');
    }
}

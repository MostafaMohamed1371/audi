<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\LocalizesAttributes;

class ProgramSectionDetail extends Model
{
    use LocalizesAttributes;

    protected $fillable = [
        'program_section_id',
        'title_ar',
        'title_en',
        'image_url',
        'intro_ar',
        'intro_en',
        'body_ar',
        'body_en',
    ];

    protected function casts(): array
    {
        return [
            'body_ar' => 'array',
            'body_en' => 'array',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(ProgramSection::class, 'program_section_id');
    }
}

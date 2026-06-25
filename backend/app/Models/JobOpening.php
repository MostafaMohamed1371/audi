<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use App\Models\Concerns\LocalizesAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobOpening extends Model
{
    use HasFactory, HasSortOrder, LocalizesAttributes;

    protected $fillable = [
        'title_ar',
        'title_en',
        'location_ar',
        'location_en',
        'employment_type',
        'summary_ar',
        'summary_en',
        'description_ar',
        'description_en',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'description_ar' => 'array',
        'description_en' => 'array',
        'is_published' => 'boolean',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }
}

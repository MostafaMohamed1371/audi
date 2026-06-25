<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\LocalizesAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalPage extends Model
{
    use HasFactory, LocalizesAttributes;

    protected $fillable = [
        'slug',
        'title_ar',
        'title_en',
        'content_ar',
        'content_en',
        'effective_date',
    ];

    protected $casts = [
        'effective_date' => 'date',
    ];
}

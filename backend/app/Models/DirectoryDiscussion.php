<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use App\Models\Concerns\LocalizesAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectoryDiscussion extends Model
{
    use HasFactory, HasSortOrder, LocalizesAttributes;

    protected $fillable = [
        'directory_type',
        'directory_number',
        'author_name_ar',
        'author_name_en',
        'body_ar',
        'body_en',
        'is_approved',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasSortOrder;

class SocialLink extends Model
{
    use HasFactory, HasSortOrder;
    protected $fillable = [
  'platform',
  'url',
  'icon',
  'sort_order',
  'is_active',
];

    protected function casts(): array
    {
        return [
  'is_active' => 'boolean',
];
    }
}

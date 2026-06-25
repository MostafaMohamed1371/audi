<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscription extends Model
{
    use HasFactory;
    protected $fillable = [
  'email',
  'locale',
  'is_confirmed',
];

    protected function casts(): array
    {
        return [
  'is_confirmed' => 'boolean',
];
    }
}

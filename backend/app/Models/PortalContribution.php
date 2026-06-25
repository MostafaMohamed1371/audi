<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalContribution extends Model
{
    use HasFactory;
    protected $fillable = [
  'type',
  'email',
  'payload',
  'status',
];

    protected function casts(): array
    {
        return [
  'payload' => 'array',
];
    }
}

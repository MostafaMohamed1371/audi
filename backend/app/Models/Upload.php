<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Upload extends Model
{
    use HasFactory;
    protected $fillable = [
  'disk',
  'path',
  'url',
  'mime_type',
  'size',
  'original_name',
  'uploaded_by',
];

    protected function casts(): array
    {
        return [
  'size' => 'integer',
];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}

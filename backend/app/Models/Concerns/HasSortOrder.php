<?php

declare(strict_types=1);

namespace App\Models\Concerns;

trait HasSortOrder
{
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}

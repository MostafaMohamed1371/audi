<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MembershipApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_name',
        'contact_name',
        'email',
        'phone',
        'country_code',
        'city',
        'message',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => MembershipApplicationStatus::class,
        ];
    }
}

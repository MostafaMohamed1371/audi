<?php

declare(strict_types=1);

namespace App\Enums;

enum MembershipApplicationStatus: string
{
    case New = 'new';
    case Reviewing = 'reviewing';
    case Approved = 'approved';
    case Rejected = 'rejected';
}

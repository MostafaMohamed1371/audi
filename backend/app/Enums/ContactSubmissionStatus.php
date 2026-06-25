<?php

declare(strict_types=1);

namespace App\Enums;

enum ContactSubmissionStatus: string
{
    case New = 'new';
    case Read = 'read';
    case Archived = 'archived';
}

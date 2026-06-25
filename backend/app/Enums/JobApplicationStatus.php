<?php

declare(strict_types=1);

namespace App\Enums;

enum JobApplicationStatus: string
{
    case New = 'new';
    case Reviewing = 'reviewing';
    case Shortlisted = 'shortlisted';
    case Rejected = 'rejected';
    case Hired = 'hired';
}

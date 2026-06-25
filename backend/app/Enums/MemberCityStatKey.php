<?php

declare(strict_types=1);

namespace App\Enums;

enum MemberCityStatKey: string
{
    case Countries = 'countries';
    case Cities = 'cities';
    case Members = 'members';
}

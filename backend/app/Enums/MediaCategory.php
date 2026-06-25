<?php

declare(strict_types=1);

namespace App\Enums;

enum MediaCategory: string
{
    case News = 'news';
    case Newsletter = 'newsletter';
    case CityMeetings = 'city_meetings';
    case SecretarySpeaks = 'secretary_speaks';
}

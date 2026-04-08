<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Contract\Enum;

enum PresetScope: string
{
    case Personal = 'personal';
    case Team = 'team';
    case Global = 'global';
}

<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Contract\Enum;

/**
 * Enumerates allowed values for preset scope in the GridPreset context.
 */
enum PresetScope: string
{
    case Personal = 'personal';
    case Team = 'team';
    case Global = 'global';
}

<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Enum;

/**
 * Enumerates allowed values for version status in the Registry context.
 */
enum VersionStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Deprecated = 'deprecated';
}

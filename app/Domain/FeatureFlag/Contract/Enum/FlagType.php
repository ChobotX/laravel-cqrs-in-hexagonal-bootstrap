<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Enum;

/**
 * Enumerates allowed values for flag type in the FeatureFlag context.
 */
enum FlagType: string
{
    case Boolean = 'boolean';
    case Select = 'select';
    case Input = 'input';
}

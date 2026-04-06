<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Enum;

enum FlagType: string
{
    case Boolean = 'boolean';
    case Select = 'select';
    case Input = 'input';
}

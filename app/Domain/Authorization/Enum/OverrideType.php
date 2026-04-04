<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Enum;

enum OverrideType: string
{
    case Grant = 'grant';
    case Deny = 'deny';
}

<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract;

enum VersionStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Deprecated = 'deprecated';
}

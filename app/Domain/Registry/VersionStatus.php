<?php

declare(strict_types=1);

namespace App\Domain\Registry;

enum VersionStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Deprecated = 'deprecated';
}

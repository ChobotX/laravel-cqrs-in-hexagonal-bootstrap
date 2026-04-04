<?php

declare(strict_types=1);

namespace App\Domain\Label\Contract\Command\RemoveLabel;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Label removal is enforced per-entity by the calling controller')]
final readonly class RemoveLabelCommand implements Command
{
    public function __construct(
        public string $labelId,
        public string $labelableId,
    ) {}
}

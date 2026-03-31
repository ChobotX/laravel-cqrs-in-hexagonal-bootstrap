<?php

declare(strict_types=1);

namespace App\Domain\Label\Command\AssignLabel;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Label assignment is enforced per-entity by the calling controller')]
final readonly class AssignLabelCommand implements Command
{
    public function __construct(
        public string $labelId,
        public string $labelableId,
        public string $expectedNamespace,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Command\DeprecateDefinitionVersion;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('registry.definitions.update')]
final readonly class DeprecateDefinitionVersionCommand implements Command
{
    public function __construct(
        public string $id,
    ) {}
}

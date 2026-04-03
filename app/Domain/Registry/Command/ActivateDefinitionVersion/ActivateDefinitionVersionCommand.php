<?php

declare(strict_types=1);

namespace App\Domain\Registry\Command\ActivateDefinitionVersion;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('registry.definitions.update')]
final readonly class ActivateDefinitionVersionCommand implements Command
{
    public function __construct(
        public string $id,
    ) {}
}

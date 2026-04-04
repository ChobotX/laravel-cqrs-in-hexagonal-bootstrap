<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Command\UpdateDefinition;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('registry.definitions.update')]
final readonly class UpdateDefinitionCommand implements Command
{
    public function __construct(
        public string $id,
        public string $name,
    ) {}
}

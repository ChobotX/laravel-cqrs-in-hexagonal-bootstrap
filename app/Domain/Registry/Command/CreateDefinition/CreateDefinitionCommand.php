<?php

declare(strict_types=1);

namespace App\Domain\Registry\Command\CreateDefinition;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('registry.definitions.create')]
final readonly class CreateDefinitionCommand implements Command
{
    public function __construct(
        public string $id,
        public string $namespace,
        public string $slug,
        public string $name,
    ) {}
}

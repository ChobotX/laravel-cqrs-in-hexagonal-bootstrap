<?php

declare(strict_types=1);

namespace App\Domain\Registry\Command\DeleteDefinition;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('registry.definitions.delete')]
final readonly class DeleteDefinitionCommand implements Command
{
    public function __construct(
        public string $id,
    ) {}
}

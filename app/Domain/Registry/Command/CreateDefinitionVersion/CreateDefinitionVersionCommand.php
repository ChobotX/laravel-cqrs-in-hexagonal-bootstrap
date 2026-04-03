<?php

declare(strict_types=1);

namespace App\Domain\Registry\Command\CreateDefinitionVersion;

use App\Application\Authorization\RequiresPermission;
use App\Domain\Registry\Schema\Schema;
use App\Contract\Command\Command;

#[RequiresPermission('registry.definitions.update')]
final readonly class CreateDefinitionVersionCommand implements Command
{
    public function __construct(
        public string $id,
        public string $definitionId,
        public Schema $schema,
    ) {}
}

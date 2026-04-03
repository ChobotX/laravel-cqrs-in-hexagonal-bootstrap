<?php

declare(strict_types=1);

namespace App\Domain\Registry\Command\CreateDefinitionVersion;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('registry.definitions.update')]
final readonly class CreateDefinitionVersionCommand implements Command
{
    /** @param array<string, mixed> $schemaData */
    public function __construct(
        public string $id,
        public string $definitionId,
        public array $schemaData,
    ) {}
}

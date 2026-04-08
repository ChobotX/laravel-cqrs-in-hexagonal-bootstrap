<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[RequiresPermission('registry.definitions.update')]
final readonly class CreateDefinitionVersionCommand implements AuditableCommand, Command
{
    /** @param array<string, mixed> $schemaData */
    public function __construct(
        public string $id,
        public string $definitionId,
        public array $schemaData,
    ) {}

    public function auditEntityType(): string
    {
        return 'definition';
    }

    public function auditEntityId(): string
    {
        return $this->id;
    }
}

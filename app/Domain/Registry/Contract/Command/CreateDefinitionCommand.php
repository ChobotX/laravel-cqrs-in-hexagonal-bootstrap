<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[RequiresPermission('registry.definitions.create')]
final readonly class CreateDefinitionCommand implements AuditableCommand, Command
{
    public function __construct(
        public string $id,
        public string $namespace,
        public string $slug,
        public string $name,
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

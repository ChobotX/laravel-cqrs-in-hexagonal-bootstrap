<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[RequiresPermission('registry.definitions.delete')]
final readonly class DeleteDefinitionCommand implements AuditableCommand, Command
{
    public function __construct(
        public string $id,
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

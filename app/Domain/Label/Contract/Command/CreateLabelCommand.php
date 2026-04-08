<?php

declare(strict_types=1);

namespace App\Domain\Label\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[RequiresPermission('labels.management.create')]
final readonly class CreateLabelCommand implements AuditableCommand, Command
{
    public function __construct(
        public string $id,
        public string $namespace,
        public string $name,
    ) {}

    public function auditEntityType(): string
    {
        return 'label';
    }

    public function auditEntityId(): string
    {
        return $this->id;
    }
}

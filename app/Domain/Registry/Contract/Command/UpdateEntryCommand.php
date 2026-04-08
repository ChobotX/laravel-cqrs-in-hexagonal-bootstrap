<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[RequiresPermission('registry.entries.update')]
final readonly class UpdateEntryCommand implements AuditableCommand, Command
{
    /** @param array<string, mixed> $data */
    public function __construct(
        public string $id,
        public string $title,
        public array $data,
    ) {}

    public function auditEntityType(): string
    {
        return 'entry';
    }

    public function auditEntityId(): string
    {
        return $this->id;
    }
}

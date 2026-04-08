<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[RequiresPermission('teams.management.delete')]
final readonly class DeleteTeamCommand implements AuditableCommand, Command
{
    public function __construct(
        public string $id,
    ) {}

    public function auditEntityType(): string
    {
        return 'team';
    }

    public function auditEntityId(): string
    {
        return $this->id;
    }
}

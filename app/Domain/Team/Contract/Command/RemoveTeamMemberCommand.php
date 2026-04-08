<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[RequiresPermission('teams.members.update')]
final readonly class RemoveTeamMemberCommand implements AuditableCommand, Command
{
    public function __construct(
        public string $userId,
        public string $teamId,
    ) {}

    public function auditEntityType(): string
    {
        return 'team';
    }

    public function auditEntityId(): string
    {
        return $this->teamId;
    }
}

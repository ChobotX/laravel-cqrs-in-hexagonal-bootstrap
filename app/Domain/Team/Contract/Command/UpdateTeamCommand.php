<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[RequiresPermission('teams.management.update')]
final readonly class UpdateTeamCommand implements AuditableCommand, Command
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public string $description,
        public ?string $parentTeamId,
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

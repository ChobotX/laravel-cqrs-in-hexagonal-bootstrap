<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Null-safe — null input means skip, controller decides based on form visibility')]
final readonly class SyncUserTeamsCommand implements AuditableCommand, Command
{
    /**
     * @param  list<string>|null  $submittedTeamIds  null = skip sync, [] = remove all
     */
    public function __construct(
        public string $userId,
        public ?array $submittedTeamIds,
        public string $actingUserId,
    ) {}

    public function auditEntityType(): string
    {
        return 'team';
    }

    public function auditEntityId(): string
    {
        return $this->userId;
    }
}

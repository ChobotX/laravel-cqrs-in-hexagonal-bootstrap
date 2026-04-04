<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command\SyncUserRoles;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Permission filtering is handled internally via assignable roles policy')]
final readonly class SyncUserRolesCommand implements Command
{
    /**
     * @param  list<string>|null  $submittedRoleIds  null = skip sync, [] = remove all assignable
     */
    public function __construct(
        public string $targetUserId,
        public ?array $submittedRoleIds,
        public string $actingUserId,
    ) {}
}

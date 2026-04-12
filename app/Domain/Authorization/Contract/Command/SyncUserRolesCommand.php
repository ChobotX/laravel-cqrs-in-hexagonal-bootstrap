<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Command payload for sync user roles in the Authorization bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Permission filtering is handled internally via assignable roles policy')]
final readonly class SyncUserRolesCommand implements Command
{
    /**
     * @param  list<string>|null  $submittedRoleIds  null = skip sync, [] = remove all assignable
     */
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $targetUserId,
        /** List of stable identifiers for batch operations. */
        public ?array $submittedRoleIds,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $actingUserId,
    ) {}
}

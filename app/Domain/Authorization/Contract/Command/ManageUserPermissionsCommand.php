<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Command\Command;
use App\Domain\Authorization\Contract\Enum\UserPermissionAction;

/**
 * Orchestrates role assignment/revocation and permission override changes for a single user
 * via one controller dispatch. Handler fans out to the concrete Assign/Revoke/Set/Remove command.
 *
 * Which fields are required depends on `action`:
 *   - AssignRole/RevokeRole    — `roleId`
 *   - SetOverride              — `permission`, `overrideType`, `overrideScope`
 *   - RemoveOverride           — `permission`
 */
#[RequiresPermission('users.roles.update')]
final readonly class ManageUserPermissionsCommand implements Command
{
    public function __construct(
        /** Target user whose roles or overrides are being changed. */
        public string $userId,
        public UserPermissionAction $action,
        /** Role id for AssignRole/RevokeRole. */
        public ?string $roleId = null,
        /** Permission key for SetOverride/RemoveOverride. */
        public ?string $permission = null,
        /** Override type for SetOverride ('grant' or 'deny'). */
        public ?string $overrideType = null,
        /** Scope string for SetOverride. */
        public ?string $overrideScope = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Enum;

/**
 * Discriminator for {@see \App\Domain\Authorization\Contract\Command\ManageUserPermissionsCommand}.
 */
enum UserPermissionAction: string
{
    case AssignRole = 'assign_role';
    case RevokeRole = 'revoke_role';
    case SetOverride = 'set_override';
    case RemoveOverride = 'remove_override';
}

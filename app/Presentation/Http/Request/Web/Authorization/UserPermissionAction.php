<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Authorization;

enum UserPermissionAction: string
{
    case AssignRole = 'assign_role';
    case RevokeRole = 'revoke_role';
    case SetOverride = 'set_override';
    case RemoveOverride = 'remove_override';
}

<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\ValueObject;

use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\Authorization\ValueObject\PermissionKey;

/**
 * Contract-level value object for role permission used across Authorization commands, queries, and events.
 */
final readonly class RolePermission
{
    public function __construct(
        /** Lookup key or configuration identifier. */
        public PermissionKey $permissionKey,
        /** Field `scope` for this contract; see module docs for validation rules. */
        public AccessScope $scope,
    ) {}
}

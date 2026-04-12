<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\ValueObject;

use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\Authorization\Enum\OverrideType;
use App\Domain\Authorization\ValueObject\PermissionKey;

/**
 * Contract-level value object for user permission override used across Authorization commands, queries, and events.
 */
final readonly class UserPermissionOverride
{
    public function __construct(
        /** Lookup key or configuration identifier. */
        public PermissionKey $permissionKey,
        /** Field `type` for this contract; see module docs for validation rules. */
        public OverrideType $type,
        /** Field `scope` for this contract; see module docs for validation rules. */
        public AccessScope $scope,
    ) {}
}

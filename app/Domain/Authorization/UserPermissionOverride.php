<?php

declare(strict_types=1);

namespace App\Domain\Authorization;

use App\Contract\Authorization\AccessScope;

final readonly class UserPermissionOverride
{
    public function __construct(
        public PermissionKey $permissionKey,
        public OverrideType $type,
        public AccessScope $scope,
    ) {}
}

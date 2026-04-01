<?php

declare(strict_types=1);

namespace App\Domain\Authorization;

use App\Contract\Authorization\AccessScope;

final readonly class RolePermission
{
    public function __construct(
        public PermissionKey $permissionKey,
        public AccessScope $scope,
    ) {}
}

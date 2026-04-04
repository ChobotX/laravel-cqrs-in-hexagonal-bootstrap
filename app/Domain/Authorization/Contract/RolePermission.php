<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract;

use App\Contract\Authorization\AccessScope;
use App\Domain\Authorization\PermissionKey;

final readonly class RolePermission
{
    public function __construct(
        public PermissionKey $permissionKey,
        public AccessScope $scope,
    ) {}
}

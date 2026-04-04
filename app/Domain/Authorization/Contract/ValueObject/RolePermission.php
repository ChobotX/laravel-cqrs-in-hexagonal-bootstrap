<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\ValueObject;

use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\Authorization\ValueObject\PermissionKey;

final readonly class RolePermission
{
    public function __construct(
        public PermissionKey $permissionKey,
        public AccessScope $scope,
    ) {}
}

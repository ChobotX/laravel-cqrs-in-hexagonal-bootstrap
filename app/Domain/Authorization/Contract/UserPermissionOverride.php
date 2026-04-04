<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract;

use App\Contract\Authorization\AccessScope;
use App\Domain\Authorization\OverrideType;
use App\Domain\Authorization\PermissionKey;

final readonly class UserPermissionOverride
{
    public function __construct(
        public PermissionKey $permissionKey,
        public OverrideType $type,
        public AccessScope $scope,
    ) {}
}

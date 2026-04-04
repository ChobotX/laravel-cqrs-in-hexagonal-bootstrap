<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\ValueObject;

use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\Authorization\Enum\OverrideType;
use App\Domain\Authorization\ValueObject\PermissionKey;

final readonly class UserPermissionOverride
{
    public function __construct(
        public PermissionKey $permissionKey,
        public OverrideType $type,
        public AccessScope $scope,
    ) {}
}

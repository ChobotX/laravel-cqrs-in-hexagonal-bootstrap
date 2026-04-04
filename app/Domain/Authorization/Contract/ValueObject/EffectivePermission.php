<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\ValueObject;

use App\Contract\Authorization\AccessScope;
use App\Domain\Authorization\ValueObject\PermissionKey;

final readonly class EffectivePermission
{
    public function __construct(
        public PermissionKey $permissionKey,
        public bool $granted,
        public AccessScope $scope,
        public string $source,
    ) {}
}

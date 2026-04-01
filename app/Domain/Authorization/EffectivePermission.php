<?php

declare(strict_types=1);

namespace App\Domain\Authorization;

use App\Contract\Authorization\AccessScope;

final readonly class EffectivePermission
{
    public function __construct(
        public PermissionKey $permissionKey,
        public bool $granted,
        public AccessScope $scope,
        public string $source,
    ) {}
}

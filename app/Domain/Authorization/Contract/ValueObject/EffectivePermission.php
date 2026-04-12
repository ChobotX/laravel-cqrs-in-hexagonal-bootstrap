<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\ValueObject;

use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\Authorization\ValueObject\PermissionKey;

/**
 * Contract-level value object for effective permission used across Authorization commands, queries, and events.
 */
final readonly class EffectivePermission
{
    public function __construct(
        /** Lookup key or configuration identifier. */
        public PermissionKey $permissionKey,
        /** Field `granted` for this contract; see module docs for validation rules. */
        public bool $granted,
        /** Field `scope` for this contract; see module docs for validation rules. */
        public AccessScope $scope,
        /** Field `source` for this contract; see module docs for validation rules. */
        public string $source,
    ) {}
}

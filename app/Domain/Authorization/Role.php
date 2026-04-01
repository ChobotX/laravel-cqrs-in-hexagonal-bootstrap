<?php

declare(strict_types=1);

namespace App\Domain\Authorization;

use App\Domain\Authorization\Contract\RoleId;

final readonly class Role
{
    /**
     * @param  list<RolePermission>  $permissions
     */
    public function __construct(
        public RoleId $id,
        public RoleName $name,
        public string $description,
        public bool $isSystem,
        public array $permissions,
    ) {}
}

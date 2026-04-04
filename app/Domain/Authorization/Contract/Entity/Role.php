<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Entity;

use App\Domain\Authorization\Contract\ValueObject\RoleId;
use App\Domain\Authorization\Contract\ValueObject\RolePermission;
use App\Domain\Authorization\ValueObject\RoleName;

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

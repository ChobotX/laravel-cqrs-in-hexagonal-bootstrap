<?php

declare(strict_types=1);

namespace App\Domain\Authorization;

final readonly class Role
{
    /**
     * @param  list<RolePermission>  $permissions
     */
    public function __construct(
        public RoleId $id,
        public ?string $organizationId,
        public RoleName $name,
        public string $description,
        public bool $isSystem,
        public array $permissions,
    ) {}
}

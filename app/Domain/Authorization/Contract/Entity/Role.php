<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Entity;

use App\Domain\Authorization\Contract\ValueObject\RoleId;
use App\Domain\Authorization\Contract\ValueObject\RolePermission;
use App\Domain\Authorization\ValueObject\RoleName;

/**
 * Immutable read-model snapshot of a Role returned from queries in the Authorization context.
 */
final readonly class Role
{
    /**
     * @param  list<RolePermission>  $permissions
     */
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public RoleId $id,
        /** Human-visible label or title. */
        public RoleName $name,
        /** Longer human-readable explanation for admin UI or emails. */
        public string $description,
        /** Boolean flag for this state or capability. */
        public bool $isSystem,
        /** Permission rows with module-defined shape (see constructor PHPDoc when present). */
        public array $permissions,
    ) {}
}

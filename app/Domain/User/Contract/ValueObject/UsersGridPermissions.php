<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\ValueObject;

/**
 * Global (non-per-row) capability flags shown in the users grid header/actions.
 */
final readonly class UsersGridPermissions
{
    public function __construct(
        public bool $canCreate,
        public bool $canUpdate,
        public bool $canDelete,
        public bool $isSuperAdmin,
    ) {}
}

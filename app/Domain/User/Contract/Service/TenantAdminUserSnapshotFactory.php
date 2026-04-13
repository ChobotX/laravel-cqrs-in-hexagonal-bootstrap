<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Service;

use App\Domain\User\Contract\Entity\User;

/**
 * Builds a {@see User} aggregate snapshot for tenant bootstrap flows that run outside the User
 * module but must not import User-internal value types directly.
 */
interface TenantAdminUserSnapshotFactory
{
    /**
     * Creates a persisted-ready user snapshot from primitive registration fields.
     */
    public function createFromPrimitives(string $id, string $name, string $email): User;
}

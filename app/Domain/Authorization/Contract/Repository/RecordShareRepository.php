<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Repository;

use App\Domain\Authorization\Enum\Action;
use App\Domain\Authorization\ValueObject\RecordShare;

/**
 * Persistence port for record share data in the Authorization context; implementations live in Infrastructure.
 */
interface RecordShareRepository
{
    /** Contract operation `share`; see infrastructure for behavior. */
    public function share(RecordShare $recordShare): void;

    /** Contract operation `revoke`; see infrastructure for behavior. */
    public function revoke(string $granteeUserId, string $resourceType, string $resourceId): void;

    /** Whether the targeted resource is present. */
    public function exists(string $granteeUserId, string $resourceType, string $resourceId): bool;

    /** @return list<RecordShare> */
    public function findByGrantee(string $granteeUserId, ?string $resourceType = null): array;

    /** @return list<string> */
    public function accessibleResourceIds(
        string $granteeUserId,
        string $resourceType,
        Action $action,
    ): array;
}

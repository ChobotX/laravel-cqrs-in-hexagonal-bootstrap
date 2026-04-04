<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Repository;

use App\Domain\Authorization\Enum\Action;
use App\Domain\Authorization\ValueObject\RecordShare;

interface RecordShareRepository
{
    public function share(RecordShare $recordShare): void;

    public function revoke(string $granteeUserId, string $resourceType, string $resourceId): void;

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

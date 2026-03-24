<?php

declare(strict_types=1);

namespace App\Domain\Authorization;

interface RecordShareRepository
{
    public function share(RecordShare $recordShare): void;

    public function revoke(string $granteeUserId, string $resourceType, string $resourceId): void;

    /** @return list<RecordShare> */
    public function findByGrantee(string $granteeUserId, ?string $resourceType = null): array;

    /** @return list<string> */
    public function accessibleResourceIds(
        string $granteeUserId,
        string $resourceType,
        Action $action,
    ): array;
}

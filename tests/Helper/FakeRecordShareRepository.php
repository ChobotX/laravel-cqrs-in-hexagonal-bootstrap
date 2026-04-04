<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Authorization\Contract\Repository\RecordShareRepository;
use App\Domain\Authorization\Enum\Action;
use App\Domain\Authorization\ValueObject\RecordShare;

final class FakeRecordShareRepository implements RecordShareRepository
{
    /** @var list<RecordShare> */
    public array $shared = [];

    /** @var list<array{granteeUserId: string, resourceType: string, resourceId: string}> */
    public array $revoked = [];

    public function share(RecordShare $recordShare): void
    {
        $this->shared[] = $recordShare;
    }

    public function revoke(string $granteeUserId, string $resourceType, string $resourceId): void
    {
        $this->revoked[] = [
            'granteeUserId' => $granteeUserId,
            'resourceType' => $resourceType,
            'resourceId' => $resourceId,
        ];
    }

    public function exists(string $granteeUserId, string $resourceType, string $resourceId): bool
    {
        return array_any(
            $this->shared,
            fn (RecordShare $recordShare): bool => $recordShare->granteeUserId === $granteeUserId
                && $recordShare->resourceType === $resourceType
                && $recordShare->resourceId === $resourceId,
        );
    }

    /** @return list<RecordShare> */
    public function findByGrantee(string $granteeUserId, ?string $resourceType = null): array
    {
        return array_values(array_filter(
            $this->shared,
            fn (RecordShare $recordShare): bool => $recordShare->granteeUserId === $granteeUserId
                && ($resourceType === null || $recordShare->resourceType === $resourceType),
        ));
    }

    /** @return list<string> */
    public function accessibleResourceIds(
        string $granteeUserId,
        string $resourceType,
        Action $action,
    ): array {
        $ids = [];

        foreach ($this->shared as $share) {
            if ($share->granteeUserId === $granteeUserId
                && $share->resourceType === $resourceType
                && $share->action === $action
            ) {
                $ids[] = $share->resourceId;
            }
        }

        return $ids;
    }
}

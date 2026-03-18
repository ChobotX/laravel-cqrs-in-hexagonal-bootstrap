<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Query\GetRecordShares;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Authorization\RecordShare;

/** @implements Query<list<RecordShare>> */
#[SkipPermissionCheck(reason: 'Record shares are queried per-resource, not globally gated')]
final readonly class GetRecordSharesQuery implements Query
{
    public function __construct(
        public string $userId,
        public string $organizationId,
        public ?string $resourceType = null,
    ) {}
}

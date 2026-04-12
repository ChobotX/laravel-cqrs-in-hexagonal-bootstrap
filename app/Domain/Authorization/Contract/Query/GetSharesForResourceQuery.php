<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Query;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Authorization\Contract\ValueObject\RecordShare;

/** @implements Query<list<RecordShare>> */
#[SkipPermissionCheck(reason: 'Resource share visibility is enforced per-resource by the controller')]
final readonly class GetSharesForResourceQuery implements Query
{
    public function __construct(
        public string $resourceType,
        public string $resourceId,
    ) {}
}

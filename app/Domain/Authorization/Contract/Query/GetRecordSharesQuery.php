<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Query;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Authorization\Contract\ValueObject\RecordShare;

/**
 * Query for get record shares in the Authorization bounded context; dispatched through the query bus.
 *
 * @implements Query<list<RecordShare>>
 */
#[SkipPermissionCheck(reason: 'Record shares are queried per-resource, not globally gated')]
final readonly class GetRecordSharesQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
        /** Classifier string or type discriminator. */
        public ?string $resourceType = null,
    ) {}
}

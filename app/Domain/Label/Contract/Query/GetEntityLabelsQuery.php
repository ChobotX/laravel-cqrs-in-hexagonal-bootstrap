<?php

declare(strict_types=1);

namespace App\Domain\Label\Contract\Query;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Query\Query;

/**
 * Query for get entity labels in the Label bounded context; dispatched through the query bus.
 *
 * @implements Query<list<\App\Domain\Label\Contract\Entity\Label>>
 */
#[SkipPermissionCheck(reason: 'Entity label loading is gated by the parent entity read permission')]
final readonly class GetEntityLabelsQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $labelableId,
    ) {}
}

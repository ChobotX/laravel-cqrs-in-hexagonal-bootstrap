<?php

declare(strict_types=1);

namespace App\Domain\Label\Contract\Query;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Label\Contract\Entity\Label;

/**
 * Query for get labels for entities in the Label bounded context; dispatched through the query bus.
 *
 * @implements Query<array<string, list<Label>>>
 */
#[SkipPermissionCheck(reason: 'Entity label loading is gated by the parent entity read permission')]
final readonly class GetLabelsForEntitiesQuery implements Query
{
    /** @param list<string> $entityIds */
    public function __construct(
        /** List of stable identifiers for batch operations. */
        public array $entityIds,
    ) {}
}

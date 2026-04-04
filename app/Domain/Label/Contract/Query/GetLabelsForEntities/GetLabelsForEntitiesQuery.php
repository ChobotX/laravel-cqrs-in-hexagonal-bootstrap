<?php

declare(strict_types=1);

namespace App\Domain\Label\Contract\Query\GetLabelsForEntities;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Label\Contract\Label;

/** @implements Query<array<string, list<Label>>> */
#[SkipPermissionCheck(reason: 'Entity label loading is gated by the parent entity read permission')]
final readonly class GetLabelsForEntitiesQuery implements Query
{
    /** @param list<string> $entityIds */
    public function __construct(
        public array $entityIds,
    ) {}
}

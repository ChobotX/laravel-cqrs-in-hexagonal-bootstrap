<?php

declare(strict_types=1);

namespace App\Domain\Label\Query\GetEntityLabels;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;

/**
 * @implements Query<list<\App\Domain\Label\Label>>
 */
#[SkipPermissionCheck(reason: 'Entity label loading is gated by the parent entity read permission')]
final readonly class GetEntityLabelsQuery implements Query
{
    public function __construct(
        public string $labelableId,
    ) {}
}

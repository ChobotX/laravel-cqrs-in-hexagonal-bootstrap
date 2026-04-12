<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Query;

use App\Application\Authorization\AccessContext;
use App\Application\Authorization\RequiresPermission;
use App\Application\Authorization\ScopeAwareQuery;
use App\Application\Authorization\ScopeTarget;
use App\Contract\Query\Query;
use App\Domain\Team\Contract\ValueObject\TeamTreeNode;

/**
 * Query for get team tree in the Team bounded context; dispatched through the query bus.
 *
 * @implements Query<list<TeamTreeNode>>
 */
#[RequiresPermission('teams.management.read')]
final readonly class GetTeamTreeQuery implements Query, ScopeAwareQuery
{
    public function __construct(
        private ?AccessContext $accessContext = null,
    ) {}

    public function scopeTarget(): ScopeTarget
    {
        return ScopeTarget::Team;
    }

    public function withAccessContext(AccessContext $accessContext): static
    {
        return new self($accessContext);
    }

    public function accessContext(): ?AccessContext
    {
        return $this->accessContext;
    }
}

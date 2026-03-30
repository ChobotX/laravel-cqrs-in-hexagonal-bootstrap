<?php

declare(strict_types=1);

namespace App\Domain\User\Query\ListUsers;

use App\Application\Authorization\AccessContext;
use App\Application\Authorization\RequiresPermission;
use App\Application\Authorization\ScopeAwareQuery;
use App\Contract\Query\Query;
use App\Domain\User\User;

/**
 * @implements Query<list<User>>
 */
#[RequiresPermission('users.list.read')]
final readonly class ListUsersQuery implements Query, ScopeAwareQuery
{
    public function __construct(
        private ?AccessContext $accessContext = null,
    ) {}

    public function withAccessContext(AccessContext $accessContext): static
    {
        return new self($accessContext);
    }

    public function accessContext(): ?AccessContext
    {
        return $this->accessContext;
    }
}

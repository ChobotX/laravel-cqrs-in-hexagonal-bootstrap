<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Query;

use App\Application\Authorization\AccessContext;
use App\Application\Authorization\RequiresPermission;
use App\Application\Authorization\ScopeAwareQuery;
use App\Application\Authorization\ScopeTarget;
use App\Contract\Query\Query;
use App\Domain\User\Contract\Entity\User;

/**
 * Query for search users in the User bounded context; dispatched through the query bus.
 *
 * @implements Query<list<User>>
 */
#[RequiresPermission('users.list.read')]
final readonly class SearchUsersQuery implements Query, ScopeAwareQuery
{
    public const int DEFAULT_LIMIT = 10;

    /**
     * @param  list<string>  $excludeUserIds
     */
    public function __construct(
        /** Field `term` for this contract; see module docs for validation rules. */
        public string $term,
        /** List of stable identifiers for batch operations. */
        public array $excludeUserIds,
        /** Field `limit` for this contract; see module docs for validation rules. */
        public int $limit = self::DEFAULT_LIMIT,
        private ?AccessContext $accessContext = null,
    ) {}

    public function scopeTarget(): ScopeTarget
    {
        return ScopeTarget::User;
    }

    public function withAccessContext(AccessContext $accessContext): static
    {
        return new self($this->term, $this->excludeUserIds, $this->limit, $accessContext);
    }

    public function accessContext(): ?AccessContext
    {
        return $this->accessContext;
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\User\Query\SearchUsers;

use App\Application\Authorization\AccessContext;
use App\Application\Authorization\RequiresPermission;
use App\Application\Authorization\ScopeAwareQuery;
use App\Contract\Query\Query;
use App\Domain\User\User;

/**
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
        public string $term,
        public array $excludeUserIds,
        public int $limit = self::DEFAULT_LIMIT,
        private ?AccessContext $accessContext = null,
    ) {}

    public function withAccessContext(AccessContext $accessContext): static
    {
        return new self($this->term, $this->excludeUserIds, $this->limit, $accessContext);
    }

    public function accessContext(): ?AccessContext
    {
        return $this->accessContext;
    }
}

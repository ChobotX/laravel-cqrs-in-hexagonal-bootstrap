<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\ValueObject\UserId;

/**
 * UserRepository fake that returns null on the first findByEmail call and the
 * configured user on subsequent calls.
 *
 * Lets tests model the "CreateUserCommand dispatch provisioned the user" flow
 * without wiring the real command bus.
 */
final class StagedUserRepository implements UserRepository
{
    public int $findByEmailCalls = 0;

    public function __construct(
        public ?User $provisionedUser = null,
    ) {}

    public function findByEmail(string $email): ?User
    {
        $this->findByEmailCalls++;

        return $this->findByEmailCalls === 1 ? null : $this->provisionedUser;
    }

    public function findById(UserId $userId): ?User
    {
        return null;
    }

    public function all(?array $onlyIds = null, array $sortings = [], array $filters = []): array
    {
        return [];
    }

    public function allPaginated(Pagination $pagination, ?array $onlyIds = null, array $sortings = [], array $filters = []): PaginatedResult
    {
        return new PaginatedResult([], 0, $pagination);
    }

    public function create(User $user): void {}

    public function update(User $user): void {}

    public function delete(UserId $userId): void {}

    public function search(string $term, array $excludeUserIds, int $limit, ?array $onlyIds = null): array
    {
        return [];
    }

    public function count(): int
    {
        return 0;
    }
}

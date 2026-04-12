<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Repository;

use App\Application\Filtering\Filter;
use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\Sorting;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\ValueObject\UserId;

/**
 * Persistence port for user data in the User context; implementations live in Infrastructure.
 */
interface UserRepository
{
    /**
     * @param  list<string>|null  $onlyIds  null = all records (no scope filter)
     * @param  list<Sorting>  $sortings
     * @param  list<Filter>  $filters
     * @return list<User>
     *                    Contract operation `all`; see infrastructure for behavior.
     */
    public function all(?array $onlyIds = null, array $sortings = [], array $filters = []): array;

    /**
     * @param  list<string>|null  $onlyIds  null = all records (no scope filter)
     * @param  list<Sorting>  $sortings
     * @param  list<Filter>  $filters
     * @return PaginatedResult<User>
     *                               Contract operation `allPaginated`; see infrastructure for behavior.
     */
    public function allPaginated(Pagination $pagination, ?array $onlyIds = null, array $sortings = [], array $filters = []): PaginatedResult;

    /** Loads a record or value object, or null when absent. */
    public function findById(UserId $userId): ?User;

    /** Loads a record or value object, or null when absent. */
    public function findByEmail(string $email): ?User;

    /** Persists a new or updated aggregate row. */
    public function create(User $user): void;

    /** Contract operation `update`; see infrastructure for behavior. */
    public function update(User $user): void;

    /** Deletes or soft-deletes the targeted record. */
    public function delete(UserId $userId): void;

    /**
     * @param  list<string>  $excludeUserIds  Users to exclude from results
     * @param  list<string>|null  $onlyIds  null = no scope filter
     * @return list<User>
     *                    Returns a filtered collection according to repository rules.
     */
    public function search(string $term, array $excludeUserIds, int $limit, ?array $onlyIds = null): array;

    /** Returns the number of matching rows. */
    public function count(): int;
}

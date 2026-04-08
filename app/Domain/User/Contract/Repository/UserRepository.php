<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Repository;

use App\Application\Filtering\Filter;
use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\Sorting;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\ValueObject\UserId;

interface UserRepository
{
    /**
     * @param  list<string>|null  $onlyIds  null = all records (no scope filter)
     * @param  list<Sorting>  $sortings
     * @param  list<Filter>  $filters
     * @return list<User>
     */
    public function all(?array $onlyIds = null, array $sortings = [], array $filters = []): array;

    /**
     * @param  list<string>|null  $onlyIds  null = all records (no scope filter)
     * @param  list<Sorting>  $sortings
     * @param  list<Filter>  $filters
     * @return PaginatedResult<User>
     */
    public function allPaginated(Pagination $pagination, ?array $onlyIds = null, array $sortings = [], array $filters = []): PaginatedResult;

    public function findById(UserId $userId): ?User;

    public function findByEmail(string $email): ?User;

    public function create(User $user): void;

    public function update(User $user): void;

    public function delete(UserId $userId): void;

    /**
     * @param  list<string>  $excludeUserIds  Users to exclude from results
     * @param  list<string>|null  $onlyIds  null = no scope filter
     * @return list<User>
     */
    public function search(string $term, array $excludeUserIds, int $limit, ?array $onlyIds = null): array;

    public function count(): int;
}
